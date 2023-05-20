
<?php
if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = 'choixInitialVisiteur';
}
$action = $_REQUEST['action'];
$titlePage = 'Validation des frais de visite';
$disabled = '';
$nbJustificatifs = '';

switch ($action) {
        // on affiche la liste déroulante pour choisir un visiteur
    case 'choixInitialVisiteur': {
            $libelleEtat = '';
            $resultatInfosVisiteur = $pdo->getInfosVisiteur();
            include("vues/v_entete.php");
            include("Vues/v_sommaire.php");
            include("Vues/v_valideFraisCorpsFiche.php");
            include("vues/v_pied.php");
            break;
        }
        // lorsque le visiteur à été séléctionné on appel la fiche de frais qui est composé de plusieurs vues
    case 'afficherFicheFraisSelectionnee': {
            // puisqu'on recharge la même page on rappel la méthode qui nous donne les infos des visiteurs
            $resultatInfosVisiteur = $pdo->getInfosVisiteur();
            // on met dans des variables de sessions id du visiteur et le mois concerné
            $idVisiteurSelectione = $_POST['listeVisiteur'];
            $moisConcerneSelectione = $_POST['txtMoisFiche'];
            visiteurSelectionne($idVisiteurSelectione, $moisConcerneSelectione);
            // on récupère grâce à la procédure stockée les infos de la fiche frais
            $infosFiche = $pdo->getInfosFicheFraisVisiteur($_SESSION['idVisiteur'], $_SESSION['moisConcerne']);

            if ($infosFiche == FALSE) {
                $libelleEtat = '00';
                $disabled = 'disabled';
                // ajouterErreur("Pas de fiche de frais pour ce visiteur ce mois");
                include("vues/v_entete.php");
                // include("vues/v_erreurs.php"); 
                include("Vues/v_sommaire.php");
                include("Vues/v_valideFraisCorpsFiche.php");
                include("vues/v_pied.php");
            } else {
                // on instancie la fiche frais
                $ficheFrais = new FicheFrais($_SESSION['idVisiteur'], $_SESSION['moisConcerne']);
                $infosLignesFF = $pdo->getLignesFF($_SESSION['idVisiteur'], $_SESSION['moisConcerne']);
                // on initialise les informations de la fiche frais pour ensuite appelé les méthodes pour retourner la bonne informations
                $ficheFrais->initAvecInfosBDD($infosFiche['FICHE_NB_JUSTIFICATIFS'], $infosFiche['FICHE_MONTANT_VALIDE'],  $infosFiche['FICHE_DATE_DERNIERE_MODIF'], $infosFiche['EFF_ID'], $infosFiche['EFF_LIBELLE'], $infosLignesFF, $infosLignesFHF = NULL, $tab = NULL);
                $libelleEtat = $ficheFrais->getLibelleEtat();
                $nbJustificatifs = $ficheFrais->getNbJustificatifs();
                $quantitesDeFraisForfaitises = $ficheFrais->getLesQuantitesDeFraisForfaitises();
                include("vues/v_entete.php");
                include("Vues/v_sommaire.php");
                include("Vues/v_valideFraisCorpsFiche.php");
                include("vues/v_pied.php");
            }


            break;
        }
        // default: {
        //         include("Vues/v_sommaire.php");
        //         break;
        //     }
}
