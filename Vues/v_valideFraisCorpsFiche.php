   <div id="contenu">
       <h2>Validation d'une fiche de frais visiteur</h2>
       <br />
       <form name="frmChoixVisiteurMoisFiche" id="frmChoixVisiteurMoisFiche" method="post" action="index.php?uc=validerFicheFrais&action=afficherFicheFraisSelectionnee">

           <?php
            if (isset($_POST['listeVisiteur'])) {
                $numVisiteur = $_SESSION['idVisiteur'];
                $moisConcerne = $_SESSION['moisConcerne'];
            } else {
                $numVisiteur = NULL;
                $moisConcerne = moisConcerne();
            }

            echo selectDepuisRecordSet('listeVisiteur1', 'Visiteur : ', 'listeVisiteur', 10, $resultatInfosVisiteur, $numVisiteur);
            ?>

           <label for="txtMoisFiche">Mois : </label>
           <input type="text" name="txtMoisFiche" id="txtMoisFiche" value="<?= $moisConcerne; ?>" readonly="readonly" />
           <input type="submit" id="btnOk" name="btnOk" value="Ok" tabindex="20" />
       </form>
       <br />
       <br />

       <?php
        include('Vues/v_valideFraisEtatFicheFrais.php');
        include('Vues/v_valideFraisForfait.php');
        include('Vues/v_valideFraisHorsForfait.php');
        include('Vues/v_valideFraisValider.php');
