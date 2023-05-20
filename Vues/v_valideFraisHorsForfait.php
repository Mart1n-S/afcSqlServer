<h2>Frais hors forfait</h2>
<form name="frmFraisHorsForfait" id="frmFraisHorsForfait" method="post" action="enregModifFHF.php" onsubmit="return confirm('Voulez-vous réellement enregistrer les modifications apportées aux frais hors forfait ?');">
    <table>
        <tr>
            <th>Date</th>
            <th>Libellé</th>
            <th>Montant</th>
            <th>Ok</th>
            <th>Reporter</th>
            <th>Supprimer</th>
        </tr>
        <tr>
            <td><input type="text" size="12" name="txtHFDate1" id="txtHFDate1" readonly="readonly" /></td>
            <td><input type="text" size="50" name="txtHFLibelle1" id="txtHFLibelle1" readonly="readonly" /></td>
            <td><input type="text" size="10" name="txtHFMontant1" id="txtHFMontant1" readonly="readonly" /></td>
            <td><input type="radio" name="rbHFAction1" value="O" tabindex="70" checked="checked" /></td>
            <td><input type="radio" name="rbHFAction1" value="R" tabindex="80" /></td>
            <td><input type="radio" name="rbHFAction1" value="S" tabindex="90" /></td>
        </tr>
        <tr>
            <td><input type="text" size="12" name="txtHFDate2" id="txtHFDate2" readonly="readonly" /></td>
            <td><input type="text" size="50" name="txtHFLibelle2" id="txtHFLibelle2" readonly="readonly" /></td>
            <td><input type="text" size="10" name="txtHFMontant2" id="txtHFMontant2" readonly="readonly" /></td>
            <td><input type="radio" name="rbHFAction2" value="O" tabindex="100" checked="checked" /></td>
            <td><input type="radio" name="rbHFAction2" value="R" tabindex="110" /></td>
            <td><input type="radio" name="rbHFAction2" value="S" tabindex="120" /></td>
        </tr>
    </table>
    <p>
        Nb de justificatifs pris en compte :&nbsp;
        <input type="text" size="4" name="txtHFNbJustificatifsPEC" value="<?= $nbJustificatifs; ?>" id="txtHFNbJustificatifsPEC" tabindex="130" /><br />

    </p>
    <p>
        <input type="submit" id="btnEnregistrerModifFHF" name="btnEnregistrerModifFHF" value="Enregistrer les modifications des lignes hors forfait" tabindex="140" <?= $disabled ?> />&nbsp;
        <input type="reset" id="btnReinitialiserFHF" name="btnReinitialiserFHF" value="Réinitialiser" tabindex="150" <?= $disabled ?> />
    </p>
</form>
</div>
<br />
<br />