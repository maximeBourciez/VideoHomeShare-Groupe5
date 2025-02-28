document.addEventListener('DOMContentLoaded', function () {
    // Variables nécessaires
    const MAX_BONNES_REPONSES = 2;
    const MAX_CARACTERES_TITRE = 50;
    const MAX_CARACTERES_DESC = 100;
    const MAX_CARACTERES_QUESTION_REPONSES = 125;

    //Gestion du formulaire de quizz
    gererCreerQuizz();

    //Gestion des décomptes
    gererDecomptes(MAX_CARACTERES_TITRE, MAX_CARACTERES_DESC, MAX_CARACTERES_QUESTION_REPONSES);

    // Gestion des réponses
    gererReponses(MAX_BONNES_REPONSES);

    // Gérer l'image
    gererImage();

    // Gérer l'intitulé de la question
    gererIntituleQuestion();
});

/**
 * @brief Méthode de gestion du formulaire du quizz
 * 
 * @returns {void}
 */
function gererCreerQuizz(){
    //Variables
    let titreQuizz = document.getElementById('titre');
    let descQuizz = document.getElementById('description');
    let btnValider = document.getElementById('validerSaisie');

    viderTextareas();

    if (titreQuizz != null && descQuizz != null && btnValider != null){
        //Etat par défaut
        btnValider.disabled = true;

        function verifierChamps(){
            let titreOk = (titreQuizz.value.length > 4);
            let descOk = (descQuizz.value.length > 4);
            btnValider.disabled = !(titreOk && descOk);
        }

        titreQuizz.addEventListener('input', verifierChamps);
        descQuizz.addEventListener('input', verifierChamps);
    }
}

/**
 * @brief Méthode de vidage des textareas
 * 
 * @returns {void}
 */
function viderTextareas() {
    // Sélectionner tous les éléments <textarea> à l'intérieur de #reponsesContainer
    const textareas = document.querySelectorAll('#reponsesContainer textarea');
    
    // Parcourir chaque <textarea> et vider son contenu
    textareas.forEach(function(textarea) {
        textarea.value = ''; // Vider le contenu
    });
}

/**
 * @brief Méthode de gestion des décomptes
 * 
 * @param {Number} //MAX_CARACTERES_TITRE, MAX_CARACTERES_DESC, MAX_CARACTERES_QUESTION_REPONSES
 * 
 * @returns {void}
 */
function gererDecomptes(MAX_CARACTERES_TITRE, MAX_CARACTERES_DESC, MAX_CARACTERES_QUESTION_REPONSES) {
    //Variables utiles
    //Quizz
    let titre = document.getElementById('titre'); //Titre du quizz
    let desc = document.getElementById('description'); //Description du quizz
    //Question
    let titreQuestion = document.getElementById('titreQuestion'); //Titre de la question
    let reponses = document.querySelectorAll('.inputReponse'); //Réponses d'une question

    //Containers
    let containerTitre = document.getElementById('containerNbDecomptesTitre');
    let containerDesc = document.getElementById('containerNbDecomptesDesc');
    let containerVal = document.getElementById('containerDecompteQuest');
    let containersRep = document.querySelectorAll('.containerNbDecomptesRep');

    //Décompte titre
    if (titre != null){
        titre.addEventListener('input', function(){
            let tailleTitre = titre.value.length;
            let nbCaracteresRestantsTitre = MAX_CARACTERES_TITRE - tailleTitre;
            
            containerTitre.textContent = `${nbCaracteresRestantsTitre} caractères restants`;
    
            if (nbCaracteresRestantsTitre < 1){
                titre.value = titre.value.substring(0, MAX_CARACTERES_TITRE);
            };
        });
    };

    //Décompte description
    if (desc != null){
        desc.addEventListener('input', function(){
            let tailleDesc = desc.value.length;
            let nbCaracteresRestantsDesc = MAX_CARACTERES_DESC - tailleDesc;
            
            containerDesc.textContent = `${nbCaracteresRestantsDesc} caractères restants`;
    
            if (nbCaracteresRestantsDesc < 1){
                desc.value = desc.value.substring(0, MAX_CARACTERES_DESC);
            };
        });
    }

    //Décompte valeur question
    if (titreQuestion != null){
        titreQuestion.addEventListener('input', function(){
            let tailleQuestion = titreQuestion.value.length;
            let nbCaracteresRestantsVal = MAX_CARACTERES_QUESTION_REPONSES - tailleQuestion;
            
            containerVal.textContent = `${nbCaracteresRestantsVal} caractères restants`;

            if (nbCaracteresRestantsVal < 1){
                titreQuestion.value = titreQuestion.value.substring(0, MAX_CARACTERES_QUESTION_REPONSES);
            };
        });
    }

    //Décompte réponses
    if (reponses != null){
        reponses.forEach((reponse, index) => {
            reponse.addEventListener('input', function(){
                let tailleRep = reponse.value.length;
                let nbCaracteresRestantsRep = MAX_CARACTERES_QUESTION_REPONSES - tailleRep;

                containersRep[index].textContent = `${nbCaracteresRestantsRep} caractères restants`;

                if (nbCaracteresRestantsRep < 1){
                    reponse.value = reponse.value.substring(0, MAX_CARACTERES_QUESTION_REPONSES);
                };
            });
        });
    }
};

/**
 * @brief Méthode de gestion des réponses
 * 
 * @param {Number} MAX_BONNES_REPONSES Nombre maximum de bonnes réponses
 * 
 * @returns {void} 
*/
function gererReponses(MAX_BONNES_REPONSES) {
    let checkboxes = document.querySelectorAll('.checkCorrect');
    let reponses = document.querySelectorAll('.inputReponse');
    let spanDanger = document.getElementById('erreurReponses');
    let btnValider = document.getElementById('validerReponses');

    // Disable le bouton par défaut
    if (btnValider != null){
        btnValider.disabled = true;
    }

    if (reponses != null){
        reponses.forEach(reponse => {
            reponse.addEventListener('input', function () {
                verifierReponses(reponses, checkboxes);
            });
        });
    }

    if (checkboxes != null){
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function () {
                // On vérifie les entrées
                verifierReponses(reponses, checkboxes);
            });
        });
    }


    /**
     * @brief Méthode de vérification des réponses
     * 
     * @param {Array} reponses Tableau des réponses
     * @param {Array} checkboxes Tableau des checkboxes
     * 
     * @returns {Boolean} Vrai si les réponses sont valides, faux sinon
     */
    function verifierReponses(reponses, checkboxes) {
        // On vérifie les entrées
        let nbReponsesRemplies = countReponsesRemplies(reponses);
        let nombreBonnesReponses = countReponsesCorrectes(checkboxes);

        // Vérification des réponses remplies
        if (nbReponsesRemplies < reponses.length) {
            // Afficher le message d'erreur
            spanDanger.classList.remove('d-none');
            spanDanger.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Veuillez remplir toutes les réponses';

            // Désactiver le bouton
            btnValider.disabled = true;
        }
        // Vérification du nombre de bonnes réponses
        else if (nombreBonnesReponses < 1 || nombreBonnesReponses > MAX_BONNES_REPONSES) {
            // Afficher le message d'erreur
            spanDanger.classList.remove('d-none');
            if (nombreBonnesReponses < 1) { spanDanger.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>Vous devez sélectionner au moins 1 bonne réponse'; }
            else { spanDanger.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i>Vous ne pouvez sélectionner que ' + MAX_BONNES_REPONSES + ' bonnes réponses'; }

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
}

/**
 * @brief Méthode de gestion d'erreurs de l'intitulé de la question
 * 
 * @returns {void}
 */
function gererIntituleQuestion() {
    let intitule = document.getElementById('titreQuestion');
    let spanDanger = document.getElementById('erreurTitre');
    let btnValider = document.getElementById('validerReponses');

    // Ajouter l'écouteur d'évènements
    if (intitule != null){
        intitule.addEventListener('input', function () {
            // On vérifie les entrées
            let longueurEntrée = intitule.value.trim().length;

            // Vérification de la longueur de l'intitulé
            if (longueurEntrée < 5) {
                // Afficher le message d'erreur
                spanDanger.classList.remove('d-none');
                spanDanger.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> L\'intitulé doit contenir au moins 5 caractères';

                // Désactiver le bouton
                btnValider.disabled = true;
            }
            // Si on a trop de caractères
            else if(longueurEntrée > 50) {
                // Afficher le message d'erreur
                spanDanger.classList.remove('d-none');
                spanDanger.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> L\'intitulé doit contenir au plus 50 caractères';

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
        });
    }
}



/**
 * @brief Méthode de gestion de l'image
 * 
 * @returns {void}
 */
function gererImage() {
    const imageInput = document.getElementById('image');
    const errorSpan = document.getElementById('erreurImage');
    const btnValider = document.getElementById('validerReponses');

    if (imageInput != null){
        imageInput.addEventListener('change', function() {
            // Vérification si un fichier a été sélectionné
            if (imageInput.files.length === 0) {
                // Afficher le message d'erreur si aucun fichier n'est sélectionné
                errorSpan.classList.remove('d-none');
                errorSpan.innerHTML = '<i class="bi bi-exclamation-triangle-fill"></i> Veuillez sélectionner une image.';
                // Désactiver le bouton
                btnValider.disabled = true;
            } else {
                // Si un fichier est sélectionné, cacher l'erreur
                errorSpan.classList.add('d-none');
                errorSpan.textContent = '';
                // Réactiver le bouton
                btnValider.disabled = false;
            }
        });
    }
}


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


