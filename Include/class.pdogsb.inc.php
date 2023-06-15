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
     * Connexion BDD
     */
    private static $serveur = 'sqlsrv:Server=Nom_Serveur';
    private static $bdd = 'Database=Nom_DataBase';
    private static $user = 'Nom_User';
    private static $mdp = 'Password';
    private static $monPdo;
    private static $monPdoGsb = null;

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
     * Retourne les informations d'un utilisateur (comptable)
     */

    public function getInfosComptable($login, $mdp)
    {
        $req = self::$monPdo->prepare("EXEC getInfosComptable :log, :mdp");
        $req->bindParam(':log', $login, PDO::PARAM_STR);
        $req->bindParam(':mdp', $mdp, PDO::PARAM_STR);
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
     * Vérifie si une fiche frais existe
     */
    public function existanceFiche($idVisiteur, $mois)
    {
        $req = self::$monPdo->prepare("EXEC existanceFiche :idVisiteur, :mois");
        $req->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
        $req->execute();
        $ligne = $req->fetch(PDO::FETCH_NUM);
        return $ligne;
    }

    /**
     * Retourne les informations d'une fiche frais en fonction du visiteur sélectionné et du mois considéré
     * self est un mot-clé en PHP qui fait référence à la classe elle-même. 
     * Il est utilisé pour accéder aux propriétés et méthodes de la classe à partir de l'intérieur de la classe elle-même.
     * Par exemple, dans la classe PdoGsb, self::$monPdoGsb est une référence à la propriété statique monPdoGsb de la classe elle-même. 
     * Cela signifie que la propriété monPdoGsb est partagée par toutes les instances de la classe PdoGsb, et peut être utilisée sans avoir besoin de créer une instance de la classe.
     */
    // getInfosFicheFraisVisiteur
    public function getInfosFiche($idVisiteur, $mois)
    {
        $req = self::$monPdo->prepare("EXEC getInfosFicheFraisVisiteur :idVisiteur, :mois");
        $req->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
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
        $req->bindParam(':idCategorie', $idCategorie, PDO::PARAM_STR);
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
        $req->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
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
        $req->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
        $req->execute();

        if ($req->rowCount() == 0) {
            return [];
        } else {
            return $req->fetchAll(PDO::FETCH_ASSOC);
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
        $req = self::$monPdo->prepare("EXEC SP_FICHE_NB_JPEC_MAJ :idVisiteur, :mois, :nbJustificatifs");
        $req->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
        $req->bindParam(':nbJustificatifs', $nbJustificatifs, PDO::PARAM_INT);
        $req->execute();
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
        try {
            self::$monPdo->beginTransaction();

            foreach ($lesFraisForfaitises as $categorie => $fraisForfait) {
                $req = self::$monPdo->prepare('EXEC SP_LIGNE_FF_MAJ :idVisiteur, :mois, :quantite, :idCategorie');
                $req->bindParam(':idVisiteur', $unIdVisiteur, PDO::PARAM_STR);
                $req->bindParam(':mois', $unMois, PDO::PARAM_STR);
                $req->bindValue(':quantite', $fraisForfait->getQuantite(), PDO::PARAM_INT);
                $req->bindParam(':idCategorie', $categorie, PDO::PARAM_STR);
                $req->execute();
            }

            self::$monPdo->commit();
            return true;
        } catch (PDOException $e) {
            self::$monPdo->rollback();
            // Ajouter le message d'erreur à la variable de requête 'erreurs'
            $errorMessage = "Erreur lors de la mise à jour des quantités des frais forfaitisés : " . $e->getMessage();
            ajouterErreur($errorMessage);
            return false;
        }
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
        try {
            self::$monPdo->beginTransaction();

            $requeteSupprimer = self::$monPdo->prepare('EXEC SP_LIGNE_FHF_SUPPRIME :idVisiteur, :mois, :numFrais');
            $requeteSupprimer->bindParam(':idVisiteur', $unIdVisiteur, PDO::PARAM_STR);
            $requeteSupprimer->bindParam(':mois', $unMois, PDO::PARAM_STR);
            $requeteSupprimer->bindParam(':numFrais', $unNumFrais, PDO::PARAM_INT);

            $requeteReporter = self::$monPdo->prepare('EXEC SP_LIGNE_FHF_REPORTE :idVisiteur, :mois, :numFrais');
            $requeteReporter->bindParam(':idVisiteur', $unIdVisiteur, PDO::PARAM_STR);
            $requeteReporter->bindParam(':mois', $unMois, PDO::PARAM_STR);
            $requeteReporter->bindParam(':numFrais', $unNumFrais, PDO::PARAM_INT);

            foreach ($lesFraisHorsForfait as $unNumFrais => $uneAction) {
                switch ($uneAction) {
                    case 'S':
                        try {
                            $requeteSupprimer->execute();
                        } catch (Exception $e) {
                            self::$monPdo->rollback();
                            $errorMessage = "Erreur lors de la suppression d'une ligne de frais hors forfait : " . $e->getMessage();
                            ajouterErreur($errorMessage);
                            return false;
                        }
                        break;
                    case 'R':
                        try {
                            $requeteReporter->execute();
                        } catch (Exception $e) {
                            self::$monPdo->rollback();
                            $errorMessage = "Erreur lors du report d'une ligne de frais hors forfait : " . $e->getMessage();
                            ajouterErreur($errorMessage);
                            return false;
                        }
                        break;
                }
            }

            $this->majNbJustificatifs($unIdVisiteur, $unMois, $nbJustificatifsPEC);

            self::$monPdo->commit();
            return true;
        } catch (PDOException $e) {
            self::$monPdo->rollback();
            $errorMessage = "Erreur lors de la mise à jour des frais hors forfait : " . $e->getMessage();
            ajouterErreur($errorMessage);
            return false;
        }
    }

    /**
     * Valide et met à jour une fiche de frais 
     */
    public function validerFicheFrais($idVisiteur, $mois, $montantValide, $idEtat)
    {
        $req = self::$monPdo->prepare("EXEC SP_FICHE_VALIDE :idVisiteur, :mois, :montantValide, :idEtat");
        $req->bindParam(':idVisiteur', $idVisiteur, PDO::PARAM_STR);
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
        $req->bindParam(':montantValide', $montantValide, PDO::PARAM_STR);
        $req->bindParam(':idEtat', $idEtat, PDO::PARAM_STR);
        $req->execute();
        $ligne = $req->fetchALL(PDO::FETCH_ASSOC);
        return $ligne;
    }

    /**
     * Détermine le nombre de fiches à clôturer pour le mois passé en paramètre
     *  @return int le nb de fiche à cloturer ['NombreFicheACloturer']
     */
    public function NbFicheACloturer($mois)
    {
        $req = self::$monPdo->prepare("EXEC F_FICHE_A_CLOTURER_NB :mois");
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
        $req->execute();
        $ligne = $req->fetch(PDO::FETCH_ASSOC);

        return $ligne['NombreFicheACloturer'];
    }

    /**
     * Retourne le nombre de fiches clôturées pour le mois passé en paramètre
     *  @return int le nb de fiche clôturées ['NombreFichesCloturees']
     */
    public function cloturerFiche($mois)
    {
        $req = self::$monPdo->prepare("EXEC SP_CLOTURER_FICHE :mois");
        $req->bindParam(':mois', $mois, PDO::PARAM_STR);
        $req->execute();

        $nombreFichesCloturees = $req->rowCount();

        return $nombreFichesCloturees;
    }
}
