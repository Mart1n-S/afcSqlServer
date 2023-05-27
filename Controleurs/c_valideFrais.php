
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
            $disabled = 'disabled';
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
                ajouterErreur("Le visiteur n'a pas crée de fiche pour ce mois.");

                include("vues/v_entete.php");
                include("vues/v_erreurs.php");
                include("Vues/v_sommaire.php");
                include("Vues/v_valideFraisCorpsFiche.php");
                include("vues/v_pied.php");
            } else {
                // sinon on instancie la fiche frais
                $ficheFrais = new FicheFrais($_SESSION['idVisiteur'], $_SESSION['moisConcerne']);
                $ficheFrais->initAvecInfosBDD();

                $libelleEtat = $ficheFrais->getLibelleEtat();
                $quantitesDeFraisForfaitises = $ficheFrais->getLesQuantitesDeFraisForfaitises();
                $lignesFHF = $ficheFrais->getLesInfosFraisHorsForfait();
                $nbJustificatifs = $ficheFrais->getNbJustificatifs();

                include("vues/v_entete.php");
                include("Vues/v_sommaire.php");
                $idEtat = $ficheFrais->getIdEtat();
                // On vérifie qu'on se trouve dans la période autorisé pour la validation de fiche de frais sinon on laisse l'accés en lecture seule
                // Si on est dans la bonne période on vérifie que la fiche à bien un idEtat à CL(cloturée) si idEtat correspond a Saisie en cours on met en lecture seule et indique qu'il faut d'abord cloturer les fiches
                // Si idEtat correspond ni à Cloturée ni à Saisie en cours on met la fiche en lecture seule en indiquant qu'elle à déjà été validé
                if (!estDansPeriodeValidation()) {
                    $disabled = 'disabled';
                    ajouterErreur("Période en dehors du 10 au 20 du mois, la fiche de frais est simplement consultable.");
                    include("vues/v_erreurs.php");
                } elseif ($idEtat == 'CR') {

                    $disabled = 'disabled';
                    ajouterErreur("La fiche n'est pas clôturée.");
                    include("vues/v_erreurs.php");
                } elseif ($idEtat != 'CL') {

                    $disabled = 'disabled';
                    ajouterErreur("La fiche a déjà été validée.");
                    include("vues/v_erreurs.php");
                }
                include("Vues/v_valideFraisCorpsFiche.php");
                include("vues/v_pied.php");
            }
            break;
        }
    case 'enregModifFF': {
            $resultatInfosVisiteur = $pdo->getInfosVisiteur();
            $ficheFrais = new FicheFrais($_SESSION['idVisiteur'], $_SESSION['moisConcerne']);
            $ficheFrais->initAvecInfosBDDSansFF();
            $libelleEtat = $ficheFrais->getLibelleEtat();

            // On prépare les frais forfaitise qui vont être mis à jour
            $ficheFrais->ajouterUnFraisForfaitise('ETP', $_REQUEST['txtEtape']);
            $ficheFrais->ajouterUnFraisForfaitise('KM ', $_REQUEST['txtKm']);
            $ficheFrais->ajouterUnFraisForfaitise('NUI', $_REQUEST['txtNuitee']);
            $ficheFrais->ajouterUnFraisForfaitise('REP', $_REQUEST['txtRepas']);

            $quantitesDeFraisForfaitises = $ficheFrais->getLesQuantitesDeFraisForfaitises();
            $lignesFHF = $ficheFrais->getLesInfosFraisHorsForfait();
            $nbJustificatifs = $ficheFrais->getNbJustificatifs();

            // On vérifie que les frais forfaitisés sont des entiers puis on les mets à jour
            if ($ficheFrais->controlerQtesFraisForfaitises()) {
                if ($ficheFrais->mettreAJourLesFraisForfaitises()) {

                    $message = "La mise à jour des frais forfaitisés a été effectuée.";

                    include("vues/v_entete.php");
                    include("vues/v_message.php");
                    include("Vues/v_sommaire.php");
                    include("Vues/v_valideFraisCorpsFiche.php");
                    include("vues/v_pied.php");
                } else {
                    include("vues/v_entete.php");
                    include("vues/v_erreurs.php");
                    include("Vues/v_sommaire.php");
                    include("Vues/v_valideFraisCorpsFiche.php");
                    include("vues/v_pied.php");
                }
            }
            break;
        }

    case 'enregModifFHF': {
            $resultatInfosVisiteur = $pdo->getInfosVisiteur();
            $ficheFrais = new FicheFrais($_SESSION['idVisiteur'], $_SESSION['moisConcerne']);
            $ficheFrais->initAvecInfosBDDSansFHF();
            $ficheFrais->setNbJustificatifs($_REQUEST['txtHFNbJustificatifsPEC']);

            $libelleEtat = $ficheFrais->getLibelleEtat();
            $quantitesDeFraisForfaitises = $ficheFrais->getLesQuantitesDeFraisForfaitises();

            // On prépare les frais hors forfait qui vont être mis à jour et on incrémente un compteur pour les FHF qui sont supprimé ou reporté 
            // afin d'indiquer dans le message de réussite combien ont été Supprimé ou reporté
            $nbFHFSupprime = 0;
            $nbFHFReporte = 0;
            foreach ($_REQUEST['tabInfosFHF'] as $uneLigne) {

                if ($uneLigne['rbHFAction'] == 'R') {
                    $nbFHFReporte = $nbFHFReporte + 1;
                } elseif ($uneLigne['rbHFAction'] == 'S') {
                    $nbFHFSupprime = $nbFHFSupprime + 1;
                }
                $ficheFrais->ajouterUnFraisHorsForfait($uneLigne['hidHFFraisNum'], $uneLigne['txtHFLibelle'], dateFrancaisVersAnglais($uneLigne['txtHFDate']), $uneLigne['txtHFMontant'], $uneLigne['rbHFAction']);
            }
            // On contrôle le nombre de justificatif si c'est bien un entier puis on met à jour les FHF (qui comprend aussi le nombre de justificatifs)
            if ($ficheFrais->controlerNbJustificatifs()) {
                if ($ficheFrais->mettreAJourLesFraisHorsForfait()) {

                    if ($nbFHFSupprime != 0 || $nbFHFReporte != 0) {
                        $message = "La mise à jour des frais hors forfait a été effectuée avec " . $nbFHFSupprime . " suppression(s) et " . $nbFHFReporte . " report(s).";
                    } else {
                        $message = "La mise à jour du nombre de justificatifs a été effectuée.";
                    }

                    // Ici on instancie une nouvelle fiche de frais avec les informations mis à jour pour les réafficher
                    $ficheFrais = new FicheFrais($_SESSION['idVisiteur'], $_SESSION['moisConcerne']);
                    $ficheFrais->initAvecInfosBDD();
                    $lignesFHF = $ficheFrais->getLesInfosFraisHorsForfait();
                    $nbJustificatifs = $ficheFrais->getNbJustificatifs();

                    include("vues/v_entete.php");
                    include("vues/v_message.php");
                    include("Vues/v_sommaire.php");
                    include("Vues/v_valideFraisCorpsFiche.php");
                    include("vues/v_pied.php");
                } else {
                    include("vues/v_entete.php");
                    include("vues/v_erreurs.php");
                    include("Vues/v_sommaire.php");
                    include("Vues/v_valideFraisCorpsFiche.php");
                    include("vues/v_pied.php");
                }
            } else {
                ajouterErreur("Le nombre de justificatif n'est pas correct");

                $lignesFHF = $ficheFrais->getLesInfosFraisHorsForfait();
                $nbJustificatifs = $ficheFrais->getNbJustificatifs();
                include("vues/v_entete.php");
                include("vues/v_erreurs.php");
                include("Vues/v_sommaire.php");
                include("Vues/v_valideFraisCorpsFiche.php");
                include("vues/v_pied.php");
            }
            break;
        }
    case 'validerFicheFrais':
        $resultatInfosVisiteur = $pdo->getInfosVisiteur();

        $ficheFrais = new FicheFrais($_SESSION['idVisiteur'], $_SESSION['moisConcerne']);
        $ficheFrais->initAvecInfosBDD();

        $libelleEtat = $ficheFrais->getLibelleEtat();
        $quantitesDeFraisForfaitises = $ficheFrais->getLesQuantitesDeFraisForfaitises();
        $lignesFHF = $ficheFrais->getLesInfosFraisHorsForfait();

        // On prépare le changement d'état de la fiche (id + libellé)
        $ficheFrais->setIdEtat('VA');
        $ficheFrais->setLibelleEtat('Validée');

        // calcul de la date actuel pour modifier la date de dernière modification -> peut être fait direct en sql 
        $timezone = new DateTimeZone('Europe/Paris');
        $date = new DateTime('now', $timezone);
        $dateActuelle = $date->format('Y-m-d');
        $ficheFrais->setDateModif($dateActuelle);

        $ficheFrais->calculerLeMontantValide();

        // Validation de la fiche de frais 
        $ficheFrais->valider();
        // on récupére le nouvel état de la fiche et le nb de justificatif 
        $libelleEtat = $ficheFrais->getLibelleEtat();
        $nbJustificatifs = $ficheFrais->getNbJustificatifs();

        // On réaffiche la fiche de frais en lecture seule
        $message = "La fiche de frais a été validée";

        $disabled = 'disabled';
        include("vues/v_entete.php");
        include("vues/v_message.php");
        include("Vues/v_sommaire.php");
        include("Vues/v_valideFraisCorpsFiche.php");
        include("vues/v_pied.php");

        break;
    default: {
            include("vues/v_entete.php");
            include("Vues/v_sommaire.php");
            include("Vues/v_valideFraisCorpsFiche.php");
            include("vues/v_pied.php");
            break;
        }
}
