
<?php
if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = 'demanderConfirmationClotureFiches';
}
$action = $_REQUEST['action'];
$titlePage = 'Cloture des frais de visite';
$titleMessage = '<div id="contenu"><h2>Cloturer les fiches de frais</h2><br>';
$disabled = '';

switch ($action) {
    case 'demanderConfirmationClotureFiches': {
            $nbFicheACloturer = $pdo->NbFicheACloturer(moisConcerne());

            if ($nbFicheACloturer == 0) {
                $message = "Il n'y a pas de fiches à cloturer pour le mois : " . moisConcerne() . ".";
                include("vues/v_entete.php");
                include("Vues/v_sommaire.php");
                include("Vues/v_message.php");
                include("vues/v_pied.php");
            } else {
                $message = "Le nombre de fiches pour le mois " . moisConcerne() . " est de : " . $nbFicheACloturer . ".";

                include("vues/v_entete.php");
                include("Vues/v_sommaire.php");
                if (!estDansPeriodeValidation()) {
                    $disabled = 'disabled';
                    ajouterErreur("La clôture des fiches de frais n'est pas possible en dehors de la période du 10 au 20 du mois.");
                    include("vues/v_erreurs.php");
                }
                include("Vues/v_message.php");
                include("Vues/v_messageOuiNon.php");
                include("vues/v_pied.php");
            }
            break;
        }
        // On cloture les fiche qui ont 'Saisie en cours' afin de pouvoir les traiter dans 'Valider Fiche de Frais'
    case 'traiterReponseClotureFiches': {
            $nbFicheCloturees = $pdo->cloturerFiche(moisConcerne());
            $message = "Le nombres de fiches cloturées est de : " . $nbFicheCloturees . ' fiches.';
            include("vues/v_entete.php");
            include("Vues/v_sommaire.php");
            include("Vues/v_message.php");
            include("vues/v_pied.php");
            break;
        }
    default: {
            include("vues/v_entete.php");
            include("vues/v_sommaire.php");
            include("vues/v_pied.php");
            break;
        }
}
