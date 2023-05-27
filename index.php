<?php

session_start();
require_once("include/fct.inc.php");
require_once("include/class.pdogsb.inc.php");
require_once("include/bibliotheques.inc.php");
require_once("include/class.FicheFrais.inc.php");

$pdo = PdoGsb::getPdoGsb();
$estConnecte = estConnecte();

if (!isset($_REQUEST['uc']) || !$estConnecte) {
    $_REQUEST['uc'] = 'connexion';
}

$uc = $_REQUEST['uc'];

switch ($uc) {
    case 'connexion': {
            include("controleurs/c_connexion.php");
            break;
        }
    case 'validerFicheFrais': {
            include("controleurs/c_valideFrais.php");
            break;
        }
    case 'cloturerSaisieFichesFrais': {

            // Si en étant sur la page de cloture on choisi OUI on appel le controleur de cloture qui va effectuer ensuite l'action de 'traiterReponseClotureFiches' c-à-d cloturer les fiches
            if (isset($_POST['BtnOui'])) {
                include('controleurs/c_clotureFicheSaisie.php');
            } else {
                // Si en étant sur la page de cloture on choisi NON cela nous ramène au 'menu' donc juste entete + sommaire + piedPage
                if (isset($_POST['BtnNon'])) {
                    $titlePage = 'Intranet du Laboratoire Galaxy-Swiss Bourdin';
                    include("vues/v_entete.php");
                    include("vues/v_sommaire.php");
                    include("vues/v_pied.php");
                } else {
                    // Si on va sur 'Clôturer la saisie des fiches de frais' pour la première fois, on appel le controleur de cloture qui va ensuite simplement afficher le formulaire qui indique 
                    // le nombre de fiche a cloturer avec OUI / NON ou qui indique qu'il n'y à pas de fiche à cloturer
                    include('controleurs/c_clotureFicheSaisie.php');
                }
            }
            break;
        }
        //	case 'gererFrais' :{
        //		include("controleurs/c_gererFrais.php");break;
        //	}
        //	case 'etatFrais' :{
        //		include("controleurs/c_etatFrais.php");break;
        //	}
}
