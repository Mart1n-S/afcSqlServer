<form method="post" name="frmChoix" id="frmChoix" action="index.php?uc=cloturerSaisieFichesFrais&action=traiterReponseClotureFiches">

    <label>Cloturer les fiches : </label>
    <input type="submit" name="BtnOui" value="Oui" onclick="return confirm('Êtes-vous sûr de vouloir clôturer les fiches ?');" <?= $disabled ?>>
    <input type="submit" name="BtnNon" value="Non" <?= $disabled ?>>
</form>
</div>