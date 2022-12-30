// gere les regles d'affichage des formulaires
let groupeID;

let quitterGroupeBtn;
let titreNomGroupe;

$(document).ready(init);

function init() {
    aGroupe = $('#ID_Groupe').text() != "";

    quitterGroupeBtn = $('#FormQuitterGroupe');
    titreNomGroupe = $("#titreNomGroupe");

    refreshGroupe()
}

function refreshGroupe() {
    $.ajax({
        url: "./PHPScripts/groupes/getGroupe.php",
        type: 'GET',
        dataType: 'json',
        success: (data) => {
            if (data.exist) {
                titreNomGroupe.text(data.groupe.Nom);
                quitterGroupeBtn.prop('disabled', false);
            }
            else {
                titreNomGroupe.text("Vous n'avez pas encore de groupe");
                quitterGroupeBtn.prop('disabled', true);
            }
        }
    })
}

function copyCodeToClipboard() {
    code = $('#copy-btn').text();
    if (!navigator.clipboard) {
        fallbackCopyTextToClipboard(code);
        return;
    }
    navigator.clipboard.writeText(code);
}