<?php

/**
 * Classe d'accès aux données.

 * Utilise les services de la classe PDO
 * pour l'application GSB
 * Les attributs sont tous statiques,
 * les 4 premiers pour la connexion
 * $monPdo de type PDO
 * $monPdoGsb qui contiendra l'unique instance de la classe

 * @package default
 * @author Cheri Bibi
 * @version    1.0
 * @link       http://www.php.net/manual/fr/book.pdo.php
 */
class PdoGsb
{
    /**
     * Connexion maison
     */
    private static $serveur = '***';
    private static $bdd = '***';
    private static $user = '***';
    private static $mdp = '***';
    private static $monPdo;
    private static $monPdoGsb = null;
    /**
     * Connexion épreuve de bts
     */
    // private static $serveur = '***';
    // private static $bdd = '***';
    // private static $user = '***';
    // private static $mdp = '***';
    // private static $monPdo;
    // private static $monPdoGsb = null;

    /**
     * $monPdo est une variable statique de la classe PdoGsb qui stocke l'instance unique de la classe PDO.
     * La classe PDO est une classe PHP qui fournit une interface pour se connecter à une base de données et effectuer des opérations de base de données. 
     * $monPdo est utilisé dans les méthodes de PdoGsb pour exécuter des requêtes sur la base de données en appelant des méthodes de la classe PDO.
     */

    /**
     * Constructeur privé, crée l'instance de PDO qui sera sollicitée
     * pour toutes les méthodes de la classe
     *
     * @version 1.1 Utilise self:: en lieu et place de PdoGsb::
     *
     */
    private function __construct()
    {
        self::$monPdo = new PDO(self::$serveur . ';' . self::$bdd, self::$user, self::$mdp);
        self::$monPdo->query("SET CHARACTER SET utf8");
    }

    public function _destruct()
    {
        self::$monPdo = null;
    }

    /**
     * Fonction statique qui crée l'unique instance de la classe

     * Appel : $instancePdoGsb = PdoGsb::getPdoGsb();

     * @return l'unique objet de la classe PdoGsb
     *
     * @version 1.1 Utilise self:: en lieu et place de PdoGsb::
     *
     */
    public static function getPdoGsb()
    {
        if (self::$monPdoGsb == null) {
            self::$monPdoGsb = new PdoGsb();
        }
        return self::$monPdoGsb;
    }

    /**
     * Retourne les informations d'un visiteur

     * @param $login
     * @param $mdp
     * @return l'id, le nom et le prénom sous la forme d'un tableau associatif
     */
    // public function getInfosVisiteur($login, $mdp)
    // {
    //     $req = "select visiteur.id as id, visiteur.nom as nom, visiteur.prenom as prenom from visiteur
    // 	where visiteur.login='$login' and visiteur.mdp='$mdp'";
    //     $rs = PdoGsb::$monPdo->query($req);
    //     $ligne = $rs->fetch();
    //     return $ligne;
    // }

    /**
     * Retourne les informations d'un utilisateur (comptable)
     */

    public function getInfosComptable($login, $mdp)
    {
        $req = self::$monPdo->prepare("EXEC getInfosComptable :log, :mdp");
        $req->bindParam(':log', $login);
        $req->bindParam(':mdp', $mdp);
        $req->execute();
        $ligne = $req->fetch();
        return $ligne;
    }

    /**
     * Retourne un tableau contenant les informations d'un visiteur
     */
    public function getInfosVisiteur()
    {
        $req = self::$monPdo->prepare("EXEC getInfosVisiteur");
        $req->execute();
        $ligne = $req->fetchAll(PDO::FETCH_NUM);
        return $ligne;
    }


    /**
     * Retourne les informations d'une fiche frais en fonction du visiteur sélectionné et du mois considéré
     * self est un mot-clé en PHP qui fait référence à la classe elle-même. 
     * Il est utilisé pour accéder aux propriétés et méthodes de la classe à partir de l'intérieur de la classe elle-même.
     * Par exemple, dans la classe PdoGsb, self::$monPdoGsb est une référence à la propriété statique monPdoGsb de la classe elle-même. 
     * Cela signifie que la propriété monPdoGsb est partagée par toutes les instances de la classe PdoGsb, et peut être utilisée sans avoir besoin de créer une instance de la classe.
     */
    public function getInfosFicheFraisVisiteur($idVisiteur, $mois)
    {
        $req = self::$monPdo->prepare("EXEC getInfosFicheFraisVisiteur :idVisiteur, :mois");
        $req->bindParam(':idVisiteur', $idVisiteur);
        $req->bindParam(':mois', $mois);
        $req->execute();
        $ligne = $req->fetch(PDO::FETCH_ASSOC);
        return $ligne;
    }

    /**
     * Retourne les informations sur la catégorie de frais en fonction de id de la catégorie de frais forfaitisé
     */
    public function getInfosCategorieFrais($idCategorie)
    {
        $req = self::$monPdo->prepare("EXEC SP_CATEGORIE_FF_GET_INFOS :idCategorie");
        $req->bindParam(':idCategorie', $idCategorie);
        $req->execute();
        $ligne = $req->fetch(PDO::FETCH_ASSOC);
        return $ligne;
    }

    /**
     * Retourne les informations sur les frais forfaitisés du visiteur
     */
    public function getLignesFF($idVisiteur, $mois)
    {
        $req = self::$monPdo->prepare("EXEC getLignesFF :idVisiteur, :mois");
        $req->bindParam(':idVisiteur', $idVisiteur);
        $req->bindParam(':mois', $mois);
        $req->execute();
        $ligne = $req->fetchALL(PDO::FETCH_ASSOC);
        return $ligne;
    }

    /**
     * Retourne les informations sur les frais hors forfait du visiteur
     * Ici on utilise la méthode rowCount si il y a des FHF on retourne un tableau associatif
     * sinon on retourne un tableau vide car il est possible qu'il n'y ai aucun FHF 
     */
    public function getLignesFHF($idVisiteur, $mois)
    {
        $req = self::$monPdo->prepare("EXEC getLignesFHF :idVisiteur, :mois");
        $req->bindParam(':idVisiteur', $idVisiteur);
        $req->bindParam(':mois', $mois);
        $req->execute();

        /**
         * rowcount() ne fonctionne pas avec sql server visiblement donc on fait ce qui suit :
         */

        $ligne = $req->fetchALL(PDO::FETCH_ASSOC);
        $rowCount = count($ligne);


        if ($rowCount <= 0) {
            $ligne = array();
        }

        // $rowCount = $req->rowCount();

        // if ($rowCount > 0) {
        //     $ligne = $req->fetchALL(PDO::FETCH_ASSOC);
        // } else {
        //     $ligne = array();
        // }

        return $ligne;
    }


    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais hors forfait
     * concernées par les deux arguments

     * La boucle foreach ne peut être utilisée ici car on procède
     * à une modification de la structure itérée - transformation du champ date-

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return tous les champs des lignes de frais hors forfait sous la forme d'un tableau associatif
     */
    public function getLesFraisHorsForfait($idVisiteur, $mois)
    {
        $req = "select * from lignefraishorsforfait where lignefraishorsforfait.idvisiteur ='$idVisiteur'
		and lignefraishorsforfait.mois = '$mois' ";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        $nbLignes = count($lesLignes);
        for ($i = 0; $i < $nbLignes; $i++) {
            $date = $lesLignes[$i]['date'];
            $lesLignes[$i]['date'] = dateAnglaisVersFrancais($date);
        }
        return $lesLignes;
    }

    /**
     * Retourne le nombre de justificatif d'un visiteur pour un mois donné

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return le nombre entier de justificatifs
     */
    public function getNbjustificatifs($idVisiteur, $mois)
    {
        $req = "select fichefrais.nbjustificatifs as nb from  fichefrais where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne['nb'];
    }

    /**
     * Retourne sous forme d'un tableau associatif toutes les lignes de frais au forfait
     * concernées par les deux arguments

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return l'id, le libelle et la quantité sous la forme d'un tableau associatif
     */
    public function getLesFraisForfait($idVisiteur, $mois)
    {
        $req = "select fraisforfait.id as idfrais, fraisforfait.libelle as libelle,
		lignefraisforfait.quantite as quantite from lignefraisforfait inner join fraisforfait
		on fraisforfait.id = lignefraisforfait.idfraisforfait
		where lignefraisforfait.idvisiteur ='$idVisiteur' and lignefraisforfait.mois='$mois'
		order by lignefraisforfait.idfraisforfait";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes;
    }

    /**
     * Retourne tous les id de la table FraisForfait

     * @return un tableau associatif
     */
    public function getLesIdFrais()
    {
        $req = "select fraisforfait.id as idfrais from fraisforfait order by fraisforfait.id";
        $res = PdoGsb::$monPdo->query($req);
        $lesLignes = $res->fetchAll();
        return $lesLignes;
    }

    /**
     * Met à jour la table ligneFraisForfait

     * Met à jour la table ligneFraisForfait pour un visiteur et
     * un mois donné en enregistrant les nouveaux montants

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @param $lesFrais tableau associatif de clé idFrais et de valeur la quantité pour ce frais
     * @return un tableau associatif
     */
    public function majFraisForfait($idVisiteur, $mois, $lesFrais)
    {
        $lesCles = array_keys($lesFrais);
        foreach ($lesCles as $unIdFrais) {
            $qte = $lesFrais[$unIdFrais];
            $req = "update lignefraisforfait set lignefraisforfait.quantite = $qte
			where lignefraisforfait.idvisiteur = '$idVisiteur' and lignefraisforfait.mois = '$mois'
			and lignefraisforfait.idfraisforfait = '$unIdFrais'";
            PdoGsb::$monPdo->exec($req);
        }
    }

    /**
     * met à jour le nombre de justificatifs de la table ficheFrais
     * pour le mois et le visiteur concerné

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     */
    public function majNbJustificatifs($idVisiteur, $mois, $nbJustificatifs)
    {
        $req = "update fichefrais set nbjustificatifs = $nbJustificatifs
		where fichefrais.idvisiteur = '$idVisiteur' and fichefrais.mois = '$mois'";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Teste si un visiteur possède une fiche de frais pour le mois passé en argument

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return vrai ou faux
     */
    public function estPremierFraisMois($idVisiteur, $mois)
    {
        $ok = false;
        $req = "select count(*) as nblignesfrais from fichefrais
		where fichefrais.mois = '$mois' and fichefrais.idvisiteur = '$idVisiteur'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        if ($laLigne['nblignesfrais'] == 0) {
            $ok = true;
        }
        return $ok;
    }

    /**
     * Retourne le dernier mois en cours d'un visiteur

     * @param $idVisiteur
     * @return le mois sous la forme aaaamm
     */
    public function dernierMoisSaisi($idVisiteur)
    {
        $req = "select max(mois) as dernierMois from fichefrais where fichefrais.idvisiteur = '$idVisiteur'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        $dernierMois = $laLigne['dernierMois'];
        return $dernierMois;
    }

    /**
     * Crée une nouvelle fiche de frais et les lignes de frais au forfait pour un visiteur et un mois donnés

     * récupère le dernier mois en cours de traitement, met à 'CL' son champs idEtat, crée une nouvelle fiche de frais
     * avec un idEtat à 'CR' et crée les lignes de frais forfait de quantités nulles
     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     */
    public function creeNouvellesLignesFrais($idVisiteur, $mois)
    {
        $dernierMois = $this->dernierMoisSaisi($idVisiteur);
        $laDerniereFiche = $this->getLesInfosFicheFrais($idVisiteur, $dernierMois);
        if ($laDerniereFiche['idEtat'] == 'CR') {
            $this->majEtatFicheFrais($idVisiteur, $dernierMois, 'CL');
        }
        $req = "insert into fichefrais(idvisiteur,mois,nbJustificatifs,montantValide,dateModif,idEtat)
		values('$idVisiteur','$mois',0,0,now(),'CR')";
        PdoGsb::$monPdo->exec($req);
        $lesIdFrais = $this->getLesIdFrais();
        foreach ($lesIdFrais as $uneLigneIdFrais) {
            $unIdFrais = $uneLigneIdFrais['idfrais'];
            $req = "insert into lignefraisforfait(idvisiteur,mois,idFraisForfait,quantite)
			values('$idVisiteur','$mois','$unIdFrais',0)";
            PdoGsb::$monPdo->exec($req);
        }
    }

    /**
     * Crée un nouveau frais hors forfait pour un visiteur un mois donné
     * à partir des informations fournies en paramètre

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @param $libelle : le libelle du frais
     * @param $date : la date du frais au format français jj//mm/aaaa
     * @param $montant : le montant
     */
    public function creeNouveauFraisHorsForfait($idVisiteur, $mois, $libelle, $date, $montant)
    {
        $dateFr = dateFrancaisVersAnglais($date);
        $req = "insert into lignefraishorsforfait
		values('','$idVisiteur','$mois','$libelle','$dateFr','$montant')";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Supprime le frais hors forfait dont l'id est passé en argument

     * @param $idFrais
     */
    public function supprimerFraisHorsForfait($idFrais)
    {
        $req = "delete from lignefraishorsforfait where lignefraishorsforfait.id =$idFrais ";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     * Retourne les mois pour lesquel un visiteur a une fiche de frais

     * @param $idVisiteur
     * @return un tableau associatif de clé un mois -aaaamm- et de valeurs l'année et le mois correspondant
     */
    public function getLesMoisDisponibles($idVisiteur)
    {
        $req = "select fichefrais.mois as mois from  fichefrais where fichefrais.idvisiteur ='$idVisiteur'
		order by fichefrais.mois desc ";
        $res = PdoGsb::$monPdo->query($req);
        $lesMois = array();
        $laLigne = $res->fetch();
        while ($laLigne != null) {
            $mois = $laLigne['mois'];
            $numAnnee = substr($mois, 0, 4);
            $numMois = substr($mois, 4, 2);
            $lesMois["$mois"] = array(
                "mois" => "$mois",
                "numAnnee" => "$numAnnee",
                "numMois" => "$numMois"
            );
            $laLigne = $res->fetch();
        }
        return $lesMois;
    }

    /**
     * Retourne les informations d'une fiche de frais d'un visiteur pour un mois donné

     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     * @return un tableau avec des champs de jointure entre une fiche de frais et la ligne d'état
     */
    public function getLesInfosFicheFrais($idVisiteur, $mois)
    {
        $req = "select ficheFrais.idEtat as idEtat, ficheFrais.dateModif as dateModif, ficheFrais.nbJustificatifs as nbJustificatifs,
			ficheFrais.montantValide as montantValide, etat.libelle as libEtat from  fichefrais inner join Etat on ficheFrais.idEtat = Etat.id
			where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        $res = PdoGsb::$monPdo->query($req);
        $laLigne = $res->fetch();
        return $laLigne;
    }

    /**
     * Modifie l'état et la date de modification d'une fiche de frais

     * Modifie le champ idEtat et met la date de modif à aujourd'hui
     * @param $idVisiteur
     * @param $mois sous la forme aaaamm
     */
    public function majEtatFicheFrais($idVisiteur, $mois, $etat)
    {
        $req = "update ficheFrais set idEtat = '$etat', dateModif = now()
		where fichefrais.idvisiteur ='$idVisiteur' and fichefrais.mois = '$mois'";
        PdoGsb::$monPdo->exec($req);
    }

    /**
     *
     * Met à jour dans la base de données les quantités des lignes de frais forfaitisées
     * pour la fiche de frais dont l'id du visiteur et le mois de la fiche sont passés en paramètre.
     * Une transaction est utilisée pour garantir que toutes les mises à jour ont bien abouti, ou aucune.
     * 
     * @param string $unIdVisiteur L'id du visiteur.
     * @param string $unMois Le mois de la fiche de frais.
     * @param array $lesFraisForfaitises Un tableau à 2 dimensions contenant pour chaque frais forfaitisé
     * le numéro de ligne et la quantité.
     * @return boolean Le résultat de la mise à jour.
     */
    public function setLesQuantitesFraisForfaitises($unIdVisiteur, $unMois, $lesFraisForfaitises)
    {
    }


    /**
     *
     * Met à jour les frais hors forfait dans la base de données.
     * La mise à jour consiste à :
     * - reporter ou supprimer certaine(s) ligne(s) des frais hors forfait ;
     * - mettre à jour le nombre de justificatifs pris en compte.
     * Une transaction est utilisée pour assurer la cohérence des données.
     * 
     * @param string $unIdVisiteur L'id du visiteur.
     * @param string $unMois Le mois de la fiche de frais.
     * @param array $lesFraisHorsForfait Un tableau à 2 dimensions contenant
     * pour chaque frais hors forfait le numéro de ligne et l'action (R ou S) à effectuer.
     * @param type $nbJustificatifsPEC Le nombre de justificatifs pris en compte.
     * @return bool Le résultat de la mise à jour (TRUE : ok ; FALSE : pas ok).
     */
    public function setLesFraisHorsForfait($unIdVisiteur, $unMois, $lesFraisHorsForfait, $nbJustificatifsPEC)
    {
    }
}
