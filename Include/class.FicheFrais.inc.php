<?php

require_once './Include/class.pdogsb.inc.php';
require_once './Include/fct.inc.php';
require_once './Include/class.Frais.inc.php';

final class FicheFrais
{

    private $idVisiteur;
    private $moisFiche;
    private $nbJustificatifs = 0;
    private $montantValide = 0;
    private $dateDerniereModif;
    private $idEtat;
    private $libelleEtat;

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
        'KM' => 2,
        'NUI' => 3,
        'REP' => 4
    ];

    private static $pdo;

    function __construct($unIdVisiteur, $unMoisFiche)
    {
        $this->idVisiteur = $unIdVisiteur;
        $this->moisFiche = $unMoisFiche;
        self::$pdo = PdoGsb::getPdoGsb();
    }

    public function initAvecInfosBDD($unNombreDeJustificatifs, $unMontantValide, $uneDateDerniereModif, $unIdEtat, $unLibelleEtat, $lesFraisForfaitises, $lesFraisHorsForfait, $unTabNumLigneFraisForfaitise)
    {

        // Initialisation des informations de la fiche
        $this->nbJustificatifs = $unNombreDeJustificatifs;
        $this->montantValide = $unMontantValide;
        $this->dateDerniereModif = $uneDateDerniereModif;
        $this->idEtat = $unIdEtat;
        $this->libelleEtat = $unLibelleEtat;
        $this->lesFraisForfaitises = $lesFraisForfaitises;
        $this->lesFraisHorsForfait = $lesFraisHorsForfait;
        self::$tabNumLigneFraisForfaitise = $unTabNumLigneFraisForfaitise;
    }

    public function initAvecInfosBDDSansFF($unNombreDeJustificatifs, $unMontantValide, $uneDateDerniereModif, $unIdEtat, $unLibelleEtat, $lesFraisHorsForfait)
    {
        $this->nbJustificatifs = $unNombreDeJustificatifs;
        $this->montantValide = $unMontantValide;
        $this->dateDerniereModif = $uneDateDerniereModif;
        $this->idEtat = $unIdEtat;
        $this->libelleEtat = $unLibelleEtat;
        $this->lesFraisHorsForfait = $lesFraisHorsForfait;
    }

    public function initInfosFicheSansLesFrais($unNombreDeJustificatifs, $uneDateDerniereModif, $unIdEtat, $unLibelleEtat)
    {

        $this->nbJustificatifs = $unNombreDeJustificatifs;
        $this->dateDerniereModif = $uneDateDerniereModif;
        $this->idEtat = $unIdEtat;
        $this->libelleEtat = $unLibelleEtat;
    }

    public function initLesFraisForfaitises($lesFraisForfaitises, $unTabNumLigneFraisForfaitise)
    {
        $this->lesFraisForfaitises = $lesFraisForfaitises;
        $this->tabNumLigneFraisForfaitise = $unTabNumLigneFraisForfaitise;
    }

    public function initLesFraisHorsForfait($lesFraisHorsForfait)
    {
        $this->lesFraisHorsForfait = $lesFraisHorsForfait;
    }

    /**
     * retourne le libellé de la fiche
     */
    public function getLibelleEtat()
    {
        return $this->libelleEtat;
    }

    /**
     * retourne le nombre de justificatifs
     */
    public function getNbJustificatifs()
    {
        return $this->nbJustificatifs;
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
        return $this->lesFraisForfaitises;
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
     * - son montant (montant).
     *
     * @return array Le tableau demandé.
     */
    public function getLesInfosFraisHorsForfait()
    {
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
    }
}
