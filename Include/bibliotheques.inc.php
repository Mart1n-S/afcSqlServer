<?php

// affiche une liste déroulante
function selectDepuisRecordSet($id, $label, $name, $tabIndex, $jeuEnregistrement, $valeurOptionnel)
{
    $codeHtml = '<label for="' . $id . '">' . $label . '</label>';
    $codeHtml .= '<select name="' . $name . '" id="' . $id . '" class="formulaireSelect" tabindex="' . $tabIndex . '">';
    foreach ($jeuEnregistrement as $row) {
        $selected = '';
        if ($row[0] == $valeurOptionnel) {
            $selected = 'selected="selected"';
        }
        $codeHtml .= '<option value="' . $row[0] . '" ' . $selected . '>' . $row[1] . '</option>' . "\n";
    }
    $codeHtml .= '</select>';

    return $codeHtml;
}

// retourne le mois concerné
function moisConcerne()
{
    $laDate = new DateTime();
    $leMois = (int) ($laDate->format('m')) - 1;
    $lAnnee = (int) ($laDate->format('Y'));
    if ($leMois == 0) {
        $lAnnee--;
        $leMois = 12;
    }
    return (new DateTime((string) $lAnnee . '-' . (string) $leMois))->format('Ym');
}
