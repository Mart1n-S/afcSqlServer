<?php

require_once './Include/class.pdogsb.inc.php';
require_once './Include/fct.inc.php';
require_once './Include/class.Frais.inc.php';
require_once './Include/class.CategorieFraisForfaitise.inc.php';

final class FicheFrais
{

    private $idVisiteur;
    private $moisFiche;
    private $nbJustificatifs = 0;
    private $montantValide = 0;
    private $dateDerniereModif;
    private $idEtat;
    private $libelleEtat;
    private static $pdo;
    /**
     * On utilise 2 collections pour stocker les frais :
     * plus efficace car on doit extraire soit les FF soit les FHF.
     * Avec une seule collection on serait toujours obligé de parcourir et
     * de tester le type de tous les frais avant de les extraires.
     *
     */
    private $lesFraisForfaitises = []; // Un tableau asociatif de la forme : clé <idCategorie> => valeur <objet FraisForfaitise>
    private $lesFraisHorsForfait = [];

    /**
     * Un tableau des numéros de ligne des frais forfaitisés.
     * Les lignes de frais forfaitisés sont numérotées en fonction de leur catégorie.
     * Le tableau est static ce qui évite de le déclarer dans chaque instance de
     * FicheFrais.
     *
     */
    static private $tabNumLigneFraisForfaitise = [
        'ETP' => 1,
        'KM ' => 2,
        'NUI' => 3,
        'REP' => 4
    ];



    function __construct($unIdVisiteur, $unMoisFiche)
    {
        $this->idVisiteur = $unIdVisiteur;
        $this->moisFiche = $unMoisFiche;
        self::$pdo = PdoGsb::getPdoGsb();
    }

    public function initAvecInfosBDD()
    {
        // Initialisation des informations de la fiche
        $this->initInfosFicheSansLesFrais();
        $this->initLesFraisForfaitises();
        $this->initLesFraisHorsForfait();
    }

    // initialise sans les FF
    public function initAvecInfosBDDSansFF()
    {
        $this->initInfosFicheSansLesFrais();
        $this->initLesFraisHorsForfait();
    }

    // initialise sans les FHF
    public function initAvecInfosBDDSansFHF()
    {
        $this->initInfosFicheSansLesFrais();
        $this->initLesFraisForfaitises();
    }

    private function initInfosFicheSansLesFrais()
    {
        $info = self::$pdo->getInfosFiche($this->idVisiteur, $this->moisFiche);

        $this->idEtat = $info['EFF_ID'];
        $this->libelleEtat = $info['EFF_LIBELLE'];
        $this->nbJustificatifs = $info['FICHE_NB_JUSTIFICATIFS'];
        $this->montantValide = $info['FICHE_MONTANT_VALIDE'];
        $this->dateDerniereModif = $info['FICHE_DATE_DERNIERE_MODIF'];
    }

    private function initLesFraisForfaitises()
    {
        $lignes = self::$pdo->getLignesFF($this->idVisiteur, $this->moisFiche);

        foreach ($lignes as $uneLigne) {
            $unFraisForfaitise = new FraisForfaitise($this->idVisiteur, $this->moisFiche, $uneLigne['FRAIS_NUM'], $uneLigne['LFF_QTE'], new CategorieFraisForfaitise($uneLigne['CFF_ID']));
            $this->lesFraisForfaitises[$uneLigne['CFF_ID']] = $unFraisForfaitise;
        }
    }

    private function initLesFraisHorsForfait()
    {
        $lignes = self::$pdo->getLignesFHF($this->idVisiteur, $this->moisFiche);

        foreach ($lignes as $uneLigne) {
            $unFraisHorsForfait = new FraisHorsForfait($this->idVisiteur, $this->moisFiche, $uneLigne['FRAIS_NUM'], $uneLigne['LFHF_LIBELLE'], $uneLigne['LFHF_DATE'], $uneLigne['LFHF_MONTANT']);
            $this->lesFraisHorsForfait[] = $unFraisHorsForfait;
        }
    }

    /**
     * retourne le libellé de la fiche
     */
    public function getLibelleEtat()
    {
        return $this->libelleEtat;
    }

    /**
     * retourne id de la fiche
     */
    public function getIdEtat()
    {
        return $this->idEtat;
    }

    /**
     * retourne le nombre de justificatifs
     */
    public function getNbJustificatifs()
    {
        return $this->nbJustificatifs;
    }

    /**
     * Met à jour le nombre de justificatifs
     */
    public function setNbJustificatifs($unNbJustificatifs)
    {
        $this->nbJustificatifs = $unNbJustificatifs;
    }

    /**
     * Contrôle du type de justificatif (entier)
     */
    public function controlerNbJustificatifs()
    {
        return estEntierPositif($this->nbJustificatifs);
    }
    /**
     *
     * Ajoute à la fiche de frais un frais forfaitisé (une ligne) dont
     * l'id de la catégorie et la quantité sont passés en paramètre.
     * Le numéro de la ligne est automatiquement calculé à partir de l'id de
     * sa catégorie.
     *
     * @param string $idCategorie L'id de la catégorie du frais forfaitisé.
     * @param int $quantite Le nombre d'unité(s).
     */
    public function ajouterUnFraisForfaitise($idCategorie, $quantite)
    {
        $numFrais = $this->getNumLigneFraisForfaitise($idCategorie);
        $this->lesFraisForfaitises[$idCategorie] = new FraisForfaitise($this->idVisiteur, $this->moisFiche, $numFrais, $quantite, new CategorieFraisForfaitise($idCategorie));
    }

    /**
     *
     * Ajoute à la fiche de frais un frais hors forfait (une ligne) dont
     * l'id de la catégorie et la quantité sont passés en paramètre.
     * Le numéro de la ligne est automatiquement calculé à partir de l'id de
     * sa catégorie.
     *
     * @param int $numFrais Le numéro de la ligne de frais hors forfait.
     * @param string $libelle Le libellé du frais.
     * @param string $date La date du frais, sous la forme AAAA-MM-JJ.
     * @param float $montant Le montant du frais.
     * @param string $action L'action à réaliser éventuellement sur le frais.
     */
    public function ajouterUnFraisHorsForfait($numFrais, $libelle, $date, $montant, $action = NULL)
    {
        $this->lesFraisHorsForfait[] = new FraisHorsForfait($this->idVisiteur, $this->moisFiche, $numFrais, $libelle, $date, $montant,  $action);
    }

    /**
     *
     * Retourne la collection des frais forfaitisés de la fiche de frais.
     *
     * @return array La collections des frais forfaitisés.
     */
    public function getLesFraisForfaitises()
    {

        return $this->lesFraisForfaitises;
    }

    /**
     *
     * Retourne un tableau contenant les quantités pour chaque ligne de frais
     * forfaitisé de la fiche de frais.
     *
     * @return array Le tableau demandé.
     */
    // public function getLesQuantitesDeFraisForfaitises($lesFraisForfaitises)
    // {
    //     return $this->lesFraisForfaitises = $lesFraisForfaitises;
    // } version V1
    public function getLesQuantitesDeFraisForfaitises()
    {
        foreach (self::$tabNumLigneFraisForfaitise as $cle => $valeur) {
            $tableau[] = $this->lesFraisForfaitises[$cle]->getQuantite();
        }
        return $tableau;
    }

    /**
     *
     * Retourne la collection des frais hors forfait de la fiche de frais.
     *
     * @return array la collections des frais hors forfait.
     */
    public function getLesFraisHorsForfait()
    {
        return $this->lesFraisHorsForfait;
    }

    /**
     *
     * Retourne un tableau associatif d'informations sur les frais hors forfait
     * de la fiche de frais :
     * - le numéro du frais (numFrais),
     * - son libellé (libelle),
     * - sa date (date),
     * - son montant (montant),
     * - l'action à réaliser(action).
     * @return array Le tableau demandé.
     */
    public function getLesInfosFraisHorsForfait()
    {
        $tabInfosFHF = [];

        foreach ($this->lesFraisHorsForfait as $FHF) {
            $tabInfosFHF[] = [
                'numFrais' => $FHF->getNumFrais(),
                'libelle' => $FHF->getLibelle(),
                'date' => dateAnglaisVersFrancais($FHF->getDate()),
                'montant' => $FHF->getMontant(),
                'action' => $FHF->getAction()
            ];
        }
        return $tabInfosFHF;
    }

    /**
     *
     * Retourne le numéro de ligne d'un frais forfaitisé dont l'identifiant de
     * la catégorie est passé en paramètre.
     * Chaque fiche de frais comporte systématiquement 4 lignes de frais forfaitisés.
     * Chaque ligne de frais forfaitisé correspond à une catégorie de frais forfaitisé.
     * Les lignes de frais forfaitisés d'une fiche sont numérotées de 1 à 4.
     * Ce numéro dépend de la catégorie de frais forfaitisé :
     * - ETP : 1,
     * - KM  : 2,
     * - NUI : 3,
     * - REP : 4.
     *
     * @param string $idCategorieFraisForfaitise L'identifiant de la catégorie de frais forfaitisé.
     * @return int Le numéro de ligne du frais.
     *
     */
    private function getNumLigneFraisForfaitise($idCategorieFraisForfaitise)
    {
        return self::$tabNumLigneFraisForfaitise[$idCategorieFraisForfaitise];
    }

    /**
     *
     * Contrôle que les quantités de frais forfaitisés passées en paramètre
     * dans un tableau sont bien des numériques entiers et positifs.
     * Cette méthode s'appuie sur la fonction lesQteFraisValides().
     *
     * @return booléen Le résultat du contrôle.
     */
    public function controlerQtesFraisForfaitises()
    {
        return lesQteFraisValides($this->getLesQuantitesDeFraisForfaitises());
    }

    /**
     *
     * Met à jour dans la base de données les quantités des lignes de frais forfaitisées.
     *
     * @return bool Le résultat de la mise à jour.
     *
     */
    public function mettreAJourLesFraisForfaitises()
    {
        return self::$pdo->setLesQuantitesFraisForfaitises($this->idVisiteur, $this->moisFiche, $this->lesFraisForfaitises) ? true : false;
    }

    /**
     *
     * Met à jour dans la base de données les frais hors forfait.
     *
     * @return bool Le résultat de la mise à jour.
     *
     */
    public function mettreAJourLesFraisHorsForfait()
    {
        foreach ($this->lesFraisHorsForfait as $unFrais) {
            $tableau[$unFrais->getNumFrais()] = $unFrais->getAction();
        }
        return  self::$pdo->setLesFraisHorsForfait($this->idVisiteur, $this->moisFiche, $tableau, $this->nbJustificatifs);
    }

    public function calculerLeMontantValide()
    {
        $frais = array_merge($this->lesFraisForfaitises, $this->lesFraisHorsForfait);
        $montantValide = 0;

        foreach ($frais as $fraisItem) {
            if ($fraisItem instanceof FraisForfaitise || ($fraisItem instanceof FraisHorsForfait && $fraisItem->getAction() == 'O')) {
                $montantValide += $fraisItem->getMontant();
            }
        }

        $this->montantValide = $montantValide;
    }

    public function setIdEtat($unID)
    {
        $this->idEtat = $unID;
    }

    public function setLibelleEtat($unLibelle)
    {
        $this->libelleEtat = $unLibelle;
    }

    public function setDateModif($uneDateDerniereModif)
    {
        $this->dateDerniereModif = $uneDateDerniereModif;
    }

    public function valider()
    {
        self::$pdo->validerFicheFrais($this->idVisiteur, $this->moisFiche, $this->montantValide, $this->idEtat, $this->dateDerniereModif);
    }
}
