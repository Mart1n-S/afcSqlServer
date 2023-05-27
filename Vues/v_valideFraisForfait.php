<h2>Frais au forfait</h2>
<form name="frmFraisForfait" id="frmFraisForfait" method="post" action="index.php?uc=validerFicheFrais&action=enregModifFF" onsubmit="return FFValide();">
    <table>
        <tr>
            <th>Forfait<br />étape</th>
            <th>Frais<br />kilométriques</th>
            <th>Nuitée<br />hôtel</th>
            <th>Repas<br />restaurant</th>
            <th></th>
        </tr>
        <tr>
            <td><input type="text" size="3" name="txtEtape" id="txtEtape" <?= $disabled ?> value="<?php if (isset($quantitesDeFraisForfaitises)) {
                                                                                                        echo $quantitesDeFraisForfaitises[0];
                                                                                                    }  ?>" tabindex="30" /></td>
            <td><input type="text" size="3" name="txtKm" id="txtKm" <?= $disabled ?> value="<?php if (isset($quantitesDeFraisForfaitises)) {
                                                                                                echo $quantitesDeFraisForfaitises[1];
                                                                                            } ?>" tabindex="35" /></td>
            <td><input type="text" size="3" name="txtNuitee" id="txtNuitee" <?= $disabled ?> value="<?php if (isset($quantitesDeFraisForfaitises)) {
                                                                                                        echo  $quantitesDeFraisForfaitises[2];
                                                                                                    } ?>" tabindex="40" /></td>
            <td><input type="text" size="3" name="txtRepas" id="txtRepas" <?= $disabled ?> value="<?php if (isset($quantitesDeFraisForfaitises)) {
                                                                                                        echo  $quantitesDeFraisForfaitises[3];
                                                                                                    } ?>" tabindex="45" /></td>
            <!-- <td><input type="text" size="3" name="txtEtape" id="txtEtape" tabindex="30" /></td>
            <td><input type="text" size="3" name="txtKm" id="txtKm" tabindex="35" /></td>
            <td><input type="text" size="3" name="txtNuitee" id="txtNuitee" tabindex="40" /></td>
            <td><input type="text" size="3" name="txtRepas" id="txtRepas" tabindex="45" /></td> -->
            <td>
                <input type="submit" id="btnEnregistrerFF" name="btnEnregistrerFF" value="Enregistrer" tabindex="50" <?= $disabled ?> />&nbsp;
                <input type="reset" id="btnReinitialiserFF" name="btnReinitialiserFF" value="Réinitialiser" tabindex="60" <?= $disabled ?> />
            </td>
        </tr>
    </table>
</form>
<br />
<br />