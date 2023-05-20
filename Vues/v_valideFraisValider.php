<div class="piedForm">
    <form name="frmValiderFicheFrais" id="frmValiderFicheFrais" method="post" action="validerFicheFrais.php" onsubmit="return confirm('Voulez-vous réellement valider la fiche de frais ?');">
        <input type="submit" name="btnValiderFiche" id="btnValiderFiche" value="Valider la fiche de frais" tabindex="160" <?= $disabled ?> />
    </form>
</div>