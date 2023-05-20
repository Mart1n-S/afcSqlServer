<?php

/**
 * Classe Frais
 *
 * Cette classe sert de base pour les frais de la fiche de frais.
 * Elle contient des attributs et des méthodes communs à toutes les classes
 * qui en héritent.
 */
abstract class Frais
{
    // Attributs protégés qui contiennent des informations sur le frais.
    protected $idVisiteur;
    protected $moisFicheFrais;
    protected $numFrais;

    /**
     * Constructeur de la classe.
     *
     *  Rappel : en PHP le constructeur est toujours nommé
     *          __construct().
     *
     */
    public function __construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais)
    {
        $this->idVisiteur = $unIdVisiteur;
        $this->moisFicheFrais = $unMoisFicheFrais;
        $this->numFrais = $unNumFrais;
    }

    /**
     * Retourne l'id du visiteur.
     *
     * @return string L'id du visiteur.
     */
    public function getIdVisiteur()
    {
        return $this->idVisiteur;
    }

    /**
     * Retourne le mois de la fiche de frais.
     *
     * @return string Le mois de la fiche.
     */
    public function getMoisFiche()
    {
        return $this->moisFicheFrais;
    }

    /**
     * Retourne le numéro du frais (de la ligne).
     *
     * @return int Le numéro du frais.
     */
    public function getNumFrais()
    {
        return $this->numFrais;
    }
    /**
     * Méthode abstraite qui doit être implémentée dans les classes qui héritent de celle-ci.
     * Elle permet de calculer le montant du frais.
     */
    // abstract public function getMontant();
}

/**
 * Classe FraisForfaitise qui hérite de la classe Frais.
 * 
 * Cette classe représente un frais forfaitisé de la fiche de frais.
 * Elle contient des attributs supplémentaires spécifiques à cette catégorie de frais.
 */

final class FraisForfaitise extends Frais
{

    // Attributs privés qui contiennent des informations spécifiques aux frais forfaitisés.
    private $quantite;
    private $laCategorieFraisForfaitise;


    /**
     * Constructeur de la classe FraisForfaitise.
     * 
     * $idVisiteur L'id du visiteur.
     * $moisFicheFrais Le mois de la fiche de frais.
     * $numFrais Le numéro du frais.
     * $quantite La quantité du frais.
     * $laCategorieFraisForfaitise La catégorie du frais forfaitisé.
     */
    public function __construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais, $uneQuantite, $uneCategorieFraisForfaitise)
    {
        // Appel au constructeur de la classe parente
        parent::__construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais);
        $this->quantite = $uneQuantite;
        $this->laCategorieFraisForfaitise = $uneCategorieFraisForfaitise;
    }

    // Méthode pour récupérer la quantité des frais forfaitisés
    public function getQuantite()
    {
        return $this->quantite;
    }

    // Méthode pour récupérer la catégorie de frais forfaitisés
    public function getLaCategorieFraisForfaitise()
    {
        return $this->laCategorieFraisForfaitise;
    }

    // Méthode pour récupérer le montant des frais forfaitisés
    // public function getMontant()
    // {
    //     // Implémentation du calcul du montant pour cette catégorie de frais
    //     switch ($this->laCategorieFraisForfaitise) {
    //         case 'ETP':
    //             $montant = 110.00;
    //             break;
    //         case 'KM':
    //             $montant = 0.62;
    //             break;
    //         case 'NUI':
    //             $montant = 80.00;
    //             break;
    //         case 'REP':
    //             $montant = 25.00;
    //             break;
    //         default:
    //             $montant = 0.00;
    //             break;
    //     }
    //     return $montant * $this->quantite;
    // }
}


/**
 * Classe FraisHorsForfait
 *
 * Cette classe représente un frais hors forfait de la fiche de frais.
 * Elle contient des attributs spécifiques à cette catégorie de frais.
 */
final class FraisHorsForfait extends Frais
{

    // Attributs privés qui contiennent des informations spécifiques aux frais hors forfait.
    private $libelle;
    private $date;
    private $montant;
    // private $action;

    /**
     * Constructeur de la classe FraisHorsForfait.
     * 
     * $idVisiteur L'id du visiteur.
     * $moisFicheFrais Le mois de la fiche de frais.
     * $numFrais Le numéro du frais.
     * $unLibelle Le libellé du frais hors forfait.
     * $uneDate La date du frais hors forfait.
     * $unMontant Le montant du frais hors forfait.
     */
    public function __construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais, $unLibelle, $uneDate, $unMontant)
    {
        // Appel au constructeur de la classe parente
        parent::__construct($unIdVisiteur, $unMoisFicheFrais, $unNumFrais);
        $this->libelle = $unLibelle;
        $this->date = $uneDate;
        $this->montant = $unMontant;
        // $this->action = $action;
    }

    // Accesseur en lecture pour le libellé
    // Retourne le libellé du frais hors forfait.
    public function getLibelle()
    {
        return $this->libelle;
    }

    // Accesseur en lecture pour la date
    // Retourne la date du frais hors forfait.
    public function getDate()
    {
        return $this->date;
    }

    // Accesseur en lecture pour le montant
    // Retourne le montant du frais hors forfait.
    public function getMontant()
    {
        return $this->montant;
    }

    // Accesseur en lecture pour l'action
    // public function getAction()
    // {
    //     return $this->action;
    // }
}
