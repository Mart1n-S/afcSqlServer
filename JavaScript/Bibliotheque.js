function FicheFraisValide() {
    if (FFSontEnregistres() && FHFSontEnregistres()) {
        return confirm('Voulez-vous réellement valider la fiche de frais ?');
    } else {
        alert('Des modifications des lignes de frais n\'ont pas encore été enregistrées');
        return false;
    }
}

function FFValide() {
    if (FFSontEnregistres()) {
        alert('Les frais forfaitisés sont déjà enregistrés.');
        return false;
    }

    return confirm('Voulez-vous réellement enregistrer les modifications apportées aux frais forfaitisés ?');
}

function FHFValide() {
    if (FHFSontEnregistres()) {
        alert('Les frais hors forfait sont déjà enregistrés.');
        return false;
    }

    return confirm('Voulez-vous réellement enregistrer les modifications apportées aux frais hors forfait ?');
}

function FFSontEnregistres() {
    const DEFAULT_VALUES = {
        etape: document.getElementById('txtEtape').defaultValue,
        km: document.getElementById('txtKm').defaultValue,
        nuitee: document.getElementById('txtNuitee').defaultValue,
        repas: document.getElementById('txtRepas').defaultValue,
    };

    const etp = document.getElementById('txtEtape').value;
    const km = document.getElementById('txtKm').value;
    const nuitee = document.getElementById('txtNuitee').value;
    const repas = document.getElementById('txtRepas').value;

    return (
        etp === DEFAULT_VALUES.etape &&
        km === DEFAULT_VALUES.km &&
        nuitee === DEFAULT_VALUES.nuitee &&
        repas === DEFAULT_VALUES.repas
    );
}

function FHFSontEnregistres() {
    const lesFHF = document.querySelectorAll('input[name*="rbHFAction"]');
    const nbJustificatifDefaultValue = document.getElementById('txtHFNbJustificatifsPEC').defaultValue;
    const nbJustificatif = document.getElementById('txtHFNbJustificatifsPEC').value;

    for (let i = 0; i < lesFHF.length; i += 3) {
        if (!lesFHF[i].checked || nbJustificatifDefaultValue != nbJustificatif) {
            return false;
        }
    }

    return true;
}