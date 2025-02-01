/**
 * @brief Gestionnaire des événements d'un thread
 * 
 * @details Ce gestionnaire d'événements est responsable de la gestion des événements suivants : 
 * - Répondre à un message (Compléter la pop-up de réponse)
 * - Signaler un message (Mettre à jour le contenu de la pop-up de signalement)
 * - Afficher / Masquer les réponses à un message (Basculer l'affichage des réponses)
 * - Mettre à jour le compteur de caractères dans les textareas des modals
 */
document.addEventListener("DOMContentLoaded", function () {
    // Sélectionne toutes les textareas dans les modals
    const textareas = document.querySelectorAll(".modal textarea");

    // Vider la textarea à chaque ouverture du modal & reset le compteur
    document.querySelectorAll(".modal").forEach(modal => {
        modal.addEventListener("show.bs.modal", () => {
            modal.querySelector("textarea").value = "";
            modal.querySelector("span").textContent = "0 / 1000";
        });
    });

    textareas.forEach(textarea => {
        // Trouve le compteur associé dans le même modal
        const counter = textarea.closest(".modal").querySelector(".modal-body span");

        textarea.addEventListener("input", () => {
            const length = textarea.value.length;
            counter.textContent = `${length} / 1000`;
        });
    });


    // Sélectionne tous les boutons "Répondre"
    const repondreButtons = document.querySelectorAll(".btn[data-bs-target='#repondreModal']");

    /**
     * @brief Ouvre la modale "Répondre" et met à jour le contenu de la modale
     * @param {Event} event
     */
    const repondreModal = document.getElementById("repondreModal");
    const modalTextarea = repondreModal.querySelector("textarea");
    const messageOriginal = repondreModal.querySelector(".message-original p");
    const auteurOriginal = repondreModal.querySelector(".message-original strong");
    const compteur = repondreModal.querySelector(".modal-body span");

    repondreButtons.forEach(button => {
        button.addEventListener("click", function () {
            // Récupérer les données associées au bouton "Répondre"
            const parentMessage = this.closest(".card-body");
            const messageText = parentMessage.querySelector("p").textContent;
            const pseudoAuteur = parentMessage.querySelector(".username-link");

            // Mettre à jour le contenu de la modale
            messageOriginal.textContent = messageText.trim();
            auteurOriginal.textContent = pseudoAuteur.textContent.trim();
            modalTextarea.placeholder = `Répondre à ${pseudoAuteur.textContent.trim()}...`;
            modalTextarea.value = "";
            compteur.textContent = "0 / 1000";
        });
    });

    // Ajout de la logique pour mettre à jour l'ID du message parent
    repondreModal.addEventListener("show.bs.modal", function (event) {
        const button = event.relatedTarget; // Bouton qui a déclenché la modale
        const idMessageParent = button.getAttribute("data-id-message-parent");
        const inputIdMessageParent = repondreModal.querySelector("input[name='id_message_parent']");

        if (inputIdMessageParent) {
            inputIdMessageParent.value = idMessageParent;
        }
    });


    // Prévention des erreurs dans le textarea de la modale "Répondre"
    let spanErrorReponse = document.getElementById("errorReponse");
    let textareaReponse = document.getElementById("reponseArea");
    let formReponse = document.getElementById("formReponse");
    const MIN_LENGTH_MESSAGE = 10;

    formReponse.addEventListener("submit", function (event) {
        console.log("Validation soumise"); // Vérifie si l'événement est bien déclenché

        let hasError = false;
        let message = textareaReponse.value.trim();

        console.log("Message après trim:", message);
        console.log("Longueur du message:", message.length);

        if (message.length < MIN_LENGTH_MESSAGE) {
            hasError = true;
            spanErrorReponse.textContent = `Votre réponse doit contenir au moins ${MIN_LENGTH_MESSAGE} caractères`;
        } else if (message.length > textareaReponse.maxLength) {
            hasError = true;
            spanErrorReponse.textContent = "Votre réponse doit contenir moins de 1000 caractères";
        } else {
            spanErrorReponse.textContent = "";
        }

        if (hasError) {
            console.log("Formulaire bloqué !");
            event.preventDefault(); // Bloque bien l'envoi
        }
    });


    /**
     * @brief Maj dynamique de la modal de signalement à chaque ouverture
     * 
     * @param {Event} event
     */
    // Écouteur d'événements pour les boutons "Signaler"
    const signalerButtons = document.querySelectorAll('[data-bs-target="#signalement"]');

    signalerButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Récupérer les données
            const messageId = this.getAttribute('data-id-message');
            const messageToSignal = document.querySelector(`.message[data-id-message="${messageId}"]`);
            var hiddenInputIdMessage = document.getElementById("id_message_signalement");

            // Mettre à jour le contenu de la modale
            if (messageToSignal) {
                // Mettre les données à jour
                const messageText = messageToSignal.querySelector('.message-text').innerText;
                document.querySelector('#signalement .message-to-signal').innerText = messageText;
                hiddenInputIdMessage.value = messageId;
            } else {
                console.log("Message Introuvable");
            }
        });
    });


    /**
     * @brief Afficahge / Masquage des réponses à un message
     */
    // Sélectionner tous les boutons toggle-responses
    const toggleButtons = document.querySelectorAll('.toggle-responses');

    // Ajouter un écouteur d'événements à chaque bouton
    toggleButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Trouver le conteneur de réponses associé
            const messageId = this.getAttribute('data-message-id');
            const responsesContainer = document.querySelector(`.responses-container[data-message-id="${messageId}"]`);

            // Vérifier si le conteneur existe
            if (responsesContainer && responsesContainer.classList.contains('responses-container')) {
                // Basculer l'affichage des réponses
                if (responsesContainer.style.display === 'none') {
                    responsesContainer.style.display = 'block';
                    this.querySelector('.show-text').style.display = 'none';
                    this.querySelector('.hide-text').style.display = 'inline';
                    this.querySelector('.bi-chevron-down').classList.replace('bi-chevron-down', 'bi-chevron-up');
                } else {
                    responsesContainer.style.display = 'none';
                    this.querySelector('.show-text').style.display = 'inline';
                    this.querySelector('.hide-text').style.display = 'none';
                    this.querySelector('.bi-chevron-up').classList.replace('bi-chevron-up', 'bi-chevron-down');
                }
            } else {
                console.error('Container de réponses non trouvé pour le message:', messageId);
            }
        });

    });
});



