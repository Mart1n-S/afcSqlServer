<!-- Division pour le sommaire -->
<div id="menuGauche">
    <div id="infosUtil">
        <h2>
            Agent comptable :<br />
            <?php echo $_SESSION['prenom'] . " " . $_SESSION['nom'] . "\n"; ?>
        </h2>
    </div>
    <ul id="menuList">
        <li>
        </li>
        <li class="smenu">
            <a href="index.php?uc=validerFicheFrais&action=choixInitialVisiteur" title="Valider des fiches de frais ">Valider des fiches de frais</a>
        </li>
        <li class="smenu">
            <a href="index.php?uc=connexion&action=deconnexion" title="Se déconnecter">Déconnexion</a>
        </li>
    </ul>
</div>