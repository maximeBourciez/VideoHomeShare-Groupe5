document.addEventListener('DOMContentLoaded', function() {
    // Variables nécessaires
    const MAX_BONNES_REPONSES = 2;

    /**
     * Vérification des données entrées pour les réponses
     */
    let checkboxes = document.querySelectorAll('.checkCorrect');
    let reponses = document.querySelectorAll('.inputReponse');
    let spanDanger = document.getElementById('erreurReponses');
    let btnValider = document.getElementById('validerReponses');

    // Disable le bouton par défaut
    btnValider.disabled = true;
    
    reponses.forEach(reponse => {
        reponse.addEventListener('input', function() {
            verifierReponses(reponses, checkboxes);
        });
    });

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // On vérifie les entrées
            verifierReponses(reponses, checkboxes);
        });
    });


    /**
 * @brief Méthode de vérification des réponses
 * 
 * @param {Array} reponses Tableau des réponses
 * @param {Array} checkboxes Tableau des checkboxes
 * 
 * @returns {Boolean} Vrai si les réponses sont valides, faux sinon
 */
function verifierReponses(reponses, checkboxes){
    // On vérifie les entrées
    let nbReponsesRemplies = countReponsesRemplies(reponses);
    let nombreBonnesReponses = countReponsesCorrectes(checkboxes);
    
    // Vérification des réponses remplies
    if(nbReponsesRemplies < reponses.length) {
        // Afficher le message d'erreur
        spanDanger.classList.remove('d-none');
        spanDanger.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Veuillez remplir toutes les réponses';

        // Désactiver le bouton
        btnValider.disabled = true;
    }
    // Vérification du nombre de bonnes réponses
    else if(nombreBonnesReponses < 1 || nombreBonnesReponses > MAX_BONNES_REPONSES) {
        // Afficher le message d'erreur
        spanDanger.classList.remove('d-none');
        if(nombreBonnesReponses < 1){ spanDanger.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>Vous devez sélectionner au moins 1 bonne réponse'; }
        else{ spanDanger.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>Vous ne pouvez sélectionner que ' + MAX_BONNES_REPONSES + ' bonnes réponses'; }

        // Désactiver le bouton
        btnValider.disabled = true;
    }
    // Si tout est bon, réactiver le bouton
    else {
        // Cacher le message d'erreur
        spanDanger.classList.add('d-none');
        spanDanger.textContent = '';

        // Réactiver le bouton
        btnValider.disabled = false;
    }
}
});


/**
 * @brief Méthode de comptage des réponses remplies
 * 
 * @param {Array} reponses Tableau des réponses
 * 
 * @returns {Number} Nombre de réponses remplies
*/
function countReponsesRemplies(reponses) {
    let nbReponsesRemplies = 0;
    reponses.forEach(reponse => {
        // On utilise trim() pour enlever les espaces avant et après
        if (reponse.value.trim() != '') {
            nbReponsesRemplies++;
        }
    });

    return nbReponsesRemplies;
}

/**
 * @brief Méthode de comptage des réponses correctes
 * 
 * @param {Array} checkboxes Tableau des checkboxes
 * 
 * @returns {Number} Nombre de réponses correctes
*/
function countReponsesCorrectes(checkboxes) {
    let nbReponsesCorrectes = 0;
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            nbReponsesCorrectes++;
        }
    });

    return nbReponsesCorrectes;
}


