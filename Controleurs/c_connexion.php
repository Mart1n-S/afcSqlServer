<?php
if (!isset($_REQUEST['action'])) {
	$_REQUEST['action'] = 'demandeConnexion';
}
$action = $_REQUEST['action'];

$titlePage = 'Intranet du Laboratoire Galaxy-Swiss Bourdin';
switch ($action) {
	case 'demandeConnexion': {
			include("vues/v_entete.php");
			include("vues/v_connexion.php");
			include("vues/v_pied.php");
			break;
		}
	case 'valideConnexion': {
			$login = $_REQUEST['login'];
			$mdp = $_REQUEST['mdp'];
			$utilisateur = $pdo->getInfosComptable($login, $mdp);
			if (!is_array($utilisateur)) {
				ajouterErreur("Login ou mot de passe incorrect");
				include("vues/v_entete.php");
				include("vues/v_erreurs.php");
				include("vues/v_connexion.php");
				include("vues/v_pied.php");
			} else {
				$id = $utilisateur['id'];
				$nom =  $utilisateur['nom'];
				$prenom = $utilisateur['prenom'];
				connecter($id, $nom, $prenom);
				include("vues/v_entete.php");
				include("vues/v_sommaire.php");
				include("vues/v_pied.php");
			}
			break;
		}

	case 'deconnexion': {
			// Code ajouté par moi. Sans cela les informations de sessions
			// ne sont pas supprimées lors d'une déconnexion.
			deconnecter();
			include("vues/v_entete.php");
			include("vues/v_connexion.php");
			include("vues/v_pied.php");
			break;
		}

	default: {
			include("vues/v_entete.php");
			include("vues/v_connexion.php");
			include("vues/v_pied.php");
			break;
		}
}
