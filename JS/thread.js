document.addEventListener("DOMContentLoaded", function () {
    const MIN_LENGTH_MESSAGE = 10;
    const MAX_LENGTH_MESSAGE = 1000;

    // 1. Gestion générique des textareas dans les modales
    const modalBodies = document.querySelectorAll(".modal");
    modalBodies.forEach(modal => {
        const textarea = modal.querySelector("textarea");
        const counter = modal.querySelector(".modal-body span, .modal-content span"); // Cherche le span dans modal-body OU modal-content
        
        // Réinitialisation à l'ouverture de la modale
        modal.addEventListener("show.bs.modal", () => {
            if (textarea && counter) {
                textarea.value = "";
                counter.textContent = `0 / ${MAX_LENGTH_MESSAGE}`;
            }
        });

        // Mise à jour du compteur lors de la saisie
        if (textarea && counter) {
            textarea.addEventListener("input", () => {
                const length = textarea.value.length;
                counter.textContent = `${length} / ${MAX_LENGTH_MESSAGE}`;
            });
        }
    });

    // 2. Fonction de configuration commune pour les modales de message
    function setupMessageModal(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return;

        const textarea = modal.querySelector('textarea[name="message"]');
        const form = modal.querySelector('form');
        const errorSpan = modal.querySelector('.text-danger');

        if (!form || !textarea || !errorSpan) return;

        // Validation du formulaire
        form.addEventListener('submit', function(event) {
            event.preventDefault();
            
            const message = textarea.value.trim();
            let hasError = false;

            if (message.length < MIN_LENGTH_MESSAGE) {
                hasError = true;
                errorSpan.textContent = `Votre message doit contenir au moins ${MIN_LENGTH_MESSAGE} caractères`;
            } else if (message.length > MAX_LENGTH_MESSAGE) {
                hasError = true;
                errorSpan.textContent = `Votre message doit contenir moins de ${MAX_LENGTH_MESSAGE} caractères`;
            } else {
                errorSpan.textContent = '';
            }

            if (!hasError) {
                this.submit();
            }
        });
    }

    // Configuration des modales de message
    if (document.getElementById('repondreModal')) {
        setupMessageModal('repondreModal');
        
        const repondreModal = document.getElementById('repondreModal');
        repondreModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const parentMessage = button.closest('.card-body');
            
            if (parentMessage) {
                const messageText = parentMessage.querySelector('p').textContent;
                const pseudoAuteur = parentMessage.querySelector('.username-link').textContent;
                const idMessageParent = button.getAttribute('data-id-message-parent');

                this.querySelector('.message-original p').textContent = messageText.trim();
                this.querySelector('.message-original strong').textContent = pseudoAuteur.trim();
                this.querySelector('textarea').placeholder = `Répondre à ${pseudoAuteur.trim()}...`;
                this.querySelector('input[name="id_message_parent"]').value = idMessageParent;
            }
        });
    }

    if (document.getElementById('nouveauMessage')) {
        setupMessageModal('nouveauMessage');
    }

    // 3. Gestion des signalements
    const signalerButtons = document.querySelectorAll('[data-bs-target="#signalement"]');
    const signalementModal = document.getElementById('signalement');
    
    if (signalementModal) {
        signalementModal.addEventListener('show.bs.modal', function() {
            const errorSpan = document.getElementById('erreurSignalement');
            if (errorSpan) {
                errorSpan.textContent = '';
            }
        });

        const formSignalement = signalementModal.querySelector('form');
        if (formSignalement) {
            formSignalement.addEventListener('submit', function(event) {
                event.preventDefault();
                
                const raisonSelect = this.querySelector('select[name="raison"]');
                const errorSpan = document.getElementById('erreurSignalement');
                
                if (raisonSelect && raisonSelect.value === '') {
                    if (errorSpan) {
                        errorSpan.textContent = 'Veuillez sélectionner une raison de signalement';
                    }
                } else {
                    this.submit();
                }
            });
        }
    }

    signalerButtons.forEach(button => {
        button.addEventListener('click', function () {
            const messageId = this.getAttribute('data-id-message');
            const messageToSignal = document.querySelector(`.message[data-id-message="${messageId}"]`);
            const hiddenInputIdMessage = document.getElementById("id_message_signalement");

            if (messageToSignal) {
                const messageText = messageToSignal.querySelector('.message-text').innerText;
                document.querySelector('#signalement .message-to-signal').innerText = messageText;
                hiddenInputIdMessage.value = messageId;
            }
        });
    });

    // 4. Gestion du toggle des réponses
    const toggleButtons = document.querySelectorAll('.toggle-responses');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function () {
            const messageId = this.getAttribute('data-message-id');
            const responsesContainer = document.querySelector(`.responses-container[data-message-id="${messageId}"]`);

            if (responsesContainer && responsesContainer.classList.contains('responses-container')) {
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
            }
        });
    });
});