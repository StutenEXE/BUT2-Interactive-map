// gere les regles d'affichage des formulaires
let groupeID;

let quitterGroupeBtn;

$(document).ready(init);

function init() {
    console.log("init")
    aGroupe = $('#ID_Groupe').text() != "";

    quitterGroupeBtn = $('#FormQuitterGroupe');
}

function quitterGroupe() {
    aGroupe = false;
    refreshButtons();
}

function rejoindreGroupe() {
    aGroupe = false;
    refreshButtons();
}

function refreshButtons() {
    if (aGroupe) {
        quitterGroupeBtn.show();
    }
    else {
        quitterGroupeBtn.hide();
    }
}