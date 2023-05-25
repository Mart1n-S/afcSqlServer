
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

            // On vérifie si une fiche existe pour le visiteur sinon on affiche 00 et on met le reste en disabled
            if ($pdo->existanceFiche($_SESSION['idVisiteur'], $_SESSION['moisConcerne'])[0] == FALSE) {
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
                $ficheFrais->initAvecInfosBDD();
                $libelleEtat = $ficheFrais->getLibelleEtat();
                $quantitesDeFraisForfaitises = $ficheFrais->getLesQuantitesDeFraisForfaitises();
                // $lignesFHF = $fiche->getLesInfosFraisHorsForfait();     à faire plus tard
                $nbJustificatifs = $ficheFrais->getNbJustificatifs();

                include("vues/v_entete.php");
                include("Vues/v_sommaire.php");
                include("Vues/v_valideFraisCorpsFiche.php");
                include("vues/v_pied.php");
            }
            break;
        }
    case 'enregModifFF': {
            $ficheFrais = new FicheFrais($_SESSION['idVisiteur'], $_SESSION['moisConcerne']);
            $ficheFrais->initAvecInfosBDDSansFF();

            $ficheFrais->ajouterUnFraisForfaitise('ETP', $_REQUEST['txtEtape']);
            $ficheFrais->ajouterUnFraisForfaitise('KM ', $_REQUEST['txtKm']);
            $ficheFrais->ajouterUnFraisForfaitise('NUI', $_REQUEST['txtNuitee']);
            $ficheFrais->ajouterUnFraisForfaitise('REP', $_REQUEST['txtRepas']);

            if ($ficheFrais->controlerQtesFraisForfaitises()) {
                if ($ficheFrais->mettreAJourLesFraisForfaitises()) {
                    echo "<h1>La mise à jour a été effectuée</h1>";
                } else {
                    include("vues/v_erreurs.php");
                }
            }
            break;
        }
        // default: {
        //         include("Vues/v_sommaire.php");
        //         break;
        //     }
}
