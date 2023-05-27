<h2>Frais hors forfait</h2>
<?php if (empty($lignesFHF)) {
    echo 'Pas de frais hors forfait';
} else { ?>
    <form name="frmFraisHorsForfait" id="frmFraisHorsForfait" method="post" action="index.php?uc=validerFicheFrais&action=enregModifFHF" onsubmit="return FHFValide();">
        <table>
            <tr>
                <th>Date</th>
                <th>Libellé</th>
                <th>Montant</th>
                <th>Ok</th>
                <th>Reporter</th>
                <th>Supprimer</th>
            </tr>
            <?php foreach ($lignesFHF as $index => $ligne) { ?>
                <tr>
                    <td><input type="text" size="12" name="tabInfosFHF[<?= $index ?>][txtHFDate]" id="txtHFDate<?= $index ?>" value="<?= $ligne['date'] ?>" readonly="readonly" /></td>
                    <td><input type="text" size="50" name="tabInfosFHF[<?= $index ?>][txtHFLibelle]" id="txtHFLibelle<?= $index ?>" value="<?= $ligne['libelle'] ?>" readonly="readonly" /></td>
                    <td><input type="text" size="10" name="tabInfosFHF[<?= $index ?>][txtHFMontant]" id="txtHFMontant<?= $index ?>" value="<?= $ligne['montant'] ?>" readonly="readonly" /></td>
                    <td><input type="radio" name="tabInfosFHF[<?= $index ?>][rbHFAction]" value="O" tabindex="<?= 70 + $index * 10 ?>" checked="checked" <?= $disabled ?> /></td>
                    <td><input type="radio" name="tabInfosFHF[<?= $index ?>][rbHFAction]" value="R" tabindex="<?= 80 + $index * 10 ?>" <?= $disabled ?> /></td>
                    <td><input type="radio" name="tabInfosFHF[<?= $index ?>][rbHFAction]" value="S" tabindex="<?= 90 + $index * 10 ?>" <?= $disabled ?> /></td>

                    <input type="hidden" name="tabInfosFHF[<?= $index ?>][hidHFFraisNum]" id="hidHFFraisNum" value="<?= $ligne['numFrais']; ?>" />
                </tr>
            <?php } ?>
        </table>
        <p>
            Nb de justificatifs pris en compte :&nbsp;
            <input type="text" size="4" name="txtHFNbJustificatifsPEC" value="<?= $nbJustificatifs; ?>" id="txtHFNbJustificatifsPEC" tabindex="130" <?= $disabled ?> /><br />

        </p>
        <p>
            <input type="submit" id="btnEnregistrerModifFHF" name="btnEnregistrerModifFHF" value="Enregistrer les modifications des lignes hors forfait" tabindex="140" <?= $disabled ?> />&nbsp;
            <input type="reset" id="btnReinitialiserFHF" name="btnReinitialiserFHF" value="Réinitialiser" tabindex="150" <?= $disabled ?> />
        </p>
    </form>
<?php } ?>
</div>
<br />
<br />