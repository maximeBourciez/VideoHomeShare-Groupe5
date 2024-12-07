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
});