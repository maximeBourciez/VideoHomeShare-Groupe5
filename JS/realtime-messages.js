/**
 * Classe permettant de gérer les messages en temps réel
 * 
 * @param {number} idFil - L'identifiant du fil de discussion
 * 
 * @property {number} idFil - L'identifiant du fil de discussion
 * @property {number} dernierMessageId - L'identifiant du dernier message récupéré
 * @property {number} interval - L'identifiant de l'intervalle de vérification des nouveaux messages
 * 
 */
class RealtimeMessages {
    /**
     * Constructeur de la classe RealtimeMessages
     * @param {*} idFil 
     */
    constructor(idFil) {
        this.idFil = idFil;
        this.dernierMessageId = this.getDernierMessageId();
        this.interval = null;
        this.initializePolling();
        this.setupDynamicEventListeners();
    }

    /**
     * Méthode permettant de récupérer l'identifiant du dernier message présent sur la page du forum
     * 
     * @returns {number} - L'identifiant du dernier message présent sur la page du forum
     */
    getDernierMessageId() {
        const messages = document.querySelectorAll('[data-id-message]');
        if (messages.length === 0) return 0;
        
        const ids = Array.from(messages).map(msg => 
            parseInt(msg.dataset.idMessage)
        );
        return Math.max(...ids);
    }

    /**
     * Méthode permettant d'initialiser le polling pour vérifier les nouveaux messages
     */
    initializePolling() {
        // Vérifie les nouveaux messages toutes les 5 secondes
        this.interval = setInterval(() => this.verifierNouveauxMessages(), 5000);
    }

    /**
     * Méthode permettant de vérifier les nouveaux messages
     * 
     * @details Cette méthode envoie une requête AJAX pour récupérer les nouveaux messages
     */
    async verifierNouveauxMessages() {
        try {
            const response = await fetch(`index.php?controller=fil&methode=getNouveauxMessages&id_fil=${this.idFil}&dernierMessageId=${this.dernierMessageId}`);
            const data = await response.json();
            
            // Parse the stringified messages
            const messages = JSON.parse(data.messages);
            
            if (messages.length > 0) {
                this.ajouterNouveauxMessages(messages);
                this.dernierMessageId = Math.max(...messages.map(m => parseInt(m.idMessage)));
                console.log('Données reçues:', messages);
            }
        } catch (error) {
            console.error('Erreur lors de la récupération des nouveaux messages:', error);
        }
    }

    /**
     * Méthode permettant d'ajouter les nouveaux messages à la page
     * 
     * @param {Array} messages 
     */
    ajouterNouveauxMessages(messages) {
        const container = document.getElementById('messageContainer');
        
        messages.forEach(message => {
            // Vérifier si le message existe déjà 
            const messageExists = document.querySelector(`[data-id-message="${message.idMessage}"]`);
            
            if (!messageExists) {
                const messageHTML = this.creerMessageHTML(message);
                
                if (message.idMessageParent) {
                    // Trouver le conteneur de réponses 
                    const parentContainer = document.querySelector(`.responses-container[data-message-id="${message.idMessageParent}"]`);
                    if (parentContainer) {
                        // Ajouter le message à la fin du conteneur de réponses parent
                        parentContainer.insertAdjacentHTML('beforeend', messageHTML);

                        // Afficher le conteneur de réponses parent si caché
                        const responsesContainer = parentContainer;

                        if (responsesContainer.style.display === 'none') {
                            // Déployer le conteneur des réponses afin qu'elle soit visible
                            responsesContainer.style.display = 'block';

                            // Vérifier si le message parent est déjà un message enfant
                            const messageParentContainer = document.getElementById(message.idMessageParent);
                            var estEnfant = messageParentContainer.classList.contains('ms-md-5'); // Indique si le message qu'on sélectione est déjà un enfant

                            // S'il n'a pas de parent, créer un toggle button si ce n'est pas déjà fait
                            if (!estEnfant) {
                                // Récupérer le toggle button
                                const toggleButton = document.querySelector(`.tooglerReponses [data-message-id="${message.idMessageParent}"]`);  

                                // S'il n'existe pas, le créer
                                if (!toggleButton) {
                                    // Créer le bouton
                                    const toggleButton = this.creerTogglerHtml(message);
                                    let toggleButtonContainer = document.querySelector(`.tooglerReponses[data-message-id="${message.idMessageParent}"]`);
                                    console.log('Création du bouton pour le message:', toggleButton);

                                    // Ajouter un écouteur d'événements pour le bouton
                                    toggleButton.addEventListener('click', function() {
                                        // Trouver le conteneur de réponses associé
                                        const messageId = this.getAttribute('data-message-id');
                                        const responsesContainer = document.querySelector(`.responses-container[data-message-id="${message.idMessageParent}"]`);
                                        
                                        // Vérifier si le conteneur existe
                                        if (responsesContainer && responsesContainer.classList.contains('responses-container')) {
                                            // Basculer l'affichage des réponses
                                            console.log('Basculer l\'affichage des réponses pour le message:', messageId);
                                            if (responsesContainer.style.display === 'block') {
                                                responsesContainer.style.display = 'none';
                                                this.querySelector('.show-text').style.display = 'inline';
                                                this.querySelector('.hide-text').style.display = 'none';
                                                this.querySelector('.bi').classList.replace('bi-chevron-up', 'bi-chevron-down');
                                            } else {
                                                responsesContainer.style.display = 'block';
                                                this.querySelector('.show-text').style.display = 'none';
                                                this.querySelector('.hide-text').style.display = 'inline';
                                                this.querySelector('.bi').classList.replace('bi-chevron-down', 'bi-chevron-up');
                                            }
                                        }
                                    });

                                    // Insérer le bouton avant le message parent
                                    toggleButtonContainer.insertAdjacentElement('afterbegin', toggleButton);
                                }    
                            }else{
                                console.error('Ce message n\'est pas parent', message);
                            }
                            
                        }
                    } else {
                        // Créer le conteneur
                        const container = createHTMLElement('div', {
                            class: 'responses-container',
                            'data-message-id': message.idMessageParent
                        });
                        container.insertAdjacentHTML('afterbegin', messageHTML);

                        console.error('Conteneur parent non trouvé pour le message:', message);
                    }
                } else {
                    container.insertAdjacentHTML('afterbegin', messageHTML);
                }
            }
        });
    }

    /**
     * Méthode permettant de créer le message HTML à partir des données du message
     * 
     * @param {Object} message - Les données du message
     * @returns {string} - Le message HTML
     * 
     */
    creerMessageHTML(message) {
        // Eviter les problemes avec l'encodage des caractères spéciaux
        const decodedMessage = message.valeur
            .replace(/&#039;/g, "'")
            .replace(/\r\n/g, "<br>");

        // Créer le message HTML
        return `
            <div id="${message.idMessage}" class="card my-4 shadow-sm border-0 rounded-3 ${message.idMessageParent ? "ms-md-5 ms-2" : ""}">
                <div class="card-body p-4 bg-mydark text-white rounded">
                    <div class="d-flex align-items-center">
                        <img src="${message.urlImageProfil || "default_avatar.jpg"}" alt="Avatar" class="rounded-circle me-3" width="50" height="50">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 mx-2 username-link">
                                <a href="index.php?controller=utilisateur&methode=show&id_utilisateur=${message.idUtilisateur}" style="color:white;" class="text-decoration-none">
                                    ${message.pseudo || "Utilisateur inconnu"}
                                </a>
                            </h5>
                            <span class="mx-2" style="font-size: 1.2em;">•</span>
                            <small>${new Date(message.dateC).toLocaleDateString()}</small>
                        </div>
                    </div>
                    <div class="message" data-id-message="${message.idMessage}">
                        <p class="mt-3 message-text">${decodedMessage}</p>
                    </div>
                    <div class="d-flex justify-content-between mt-4">
                        <!-- Boutons like, dislike, répondre -->
                        <div class="d-flex flex-wrap gap-2 ${ message.idMessageParent ? '' : 'likesContainer' }">
                            <form method="POST" action="index.php?controller=fil&methode=like">
                                <input type="hidden" name="id_message" value="${message.idMessage}">
                                <input type="hidden" name="id_fil" value="${this.idFil}">
                                <button class="btn action-btn" type="submit">
                                    <i class="bi bi-hand-thumbs-up"></i>
                                    ${message.nbLikes > 0 ? `<span class="count">${message.nbLikes}</span>` : ""}
                                </button>
                            </form>

                            <form method="POST" action="index.php?controller=fil&methode=dislike">
                                <input type="hidden" name="id_message" value="${message.idMessage}">
                                <input type="hidden" name="id_fil" value="${this.idFil}">
                                <button class="btn action-btn" type="submit">
                                    <i class="bi bi-hand-thumbs-down"></i>
                                    ${message.nbDislikes > 0 ? `<span class="count">${message.nbDislikes}</span>` : ""}
                                </button>
                            </form>

                            <button class="btn action-btn" data-bs-toggle="modal" data-bs-target="#repondreModal" data-id-message-parent="${message.idMessage}">
                                <i class="bi bi-reply"></i>
                                <span class="btn-text">Répondre</span>
                            </button>
                        </div>

                        ${message.idMessageParent ? "" : `<div class="tooglerReponses mt-3" data-message-id="${message.idMessage}"></div>`}

                        <!-- Boutons supprimer/signaler -->
                        <div class="d-flex gap-2">
                            <form method="POST" action="index.php?controller=fil&methode=supprimerMessage" onsubmit="return confirmerSuppression('${message.idMessage}');">
                                <input type="hidden" name="idMessage" value="${message.idMessage}">
                                <input type="hidden" name="id_fil" value="${this.idFil}">
                                <button class="btn action-btn warning-btn">
                                    <i class="bi bi-trash"></i>
                                    <span class="btn-text">Supprimer</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="responses-container" style="display: none;" data-message-id="${message.idMessage}">
            </div>
        `;
    }

    /**
     * Méthode permettant de créer le toggler HTML pour les réponses
     * 
     * @param {Object} message - Les données du message
     * @returns {HTMLElement} - Le bouton pour afficher/masquer les réponses
     */
    creerTogglerHtml(message) {
        // Créer la span 
        const button = document.createElement('span');
        button.className = 'toggle-responses text-light my-2';
        button.setAttribute('data-message-id', message.idMessage);

        // Créer l'icône
        const icon = document.createElement('i');
        icon.className = 'bi bi-chevron-down';
        button.appendChild(icon);

        // Créer le texte (voir les réponses)
        const showText = document.createElement('span');
        showText.className = 'show-text';
        showText.textContent = 'Voir les réponses';
        showText.style.display = 'none';
        button.appendChild(showText);

        // Créer le texte (masquer les réponses)
        const hideText = document.createElement('span');
        hideText.className = 'hide-text';
        hideText.textContent = 'Masquer les réponses';
        hideText.style.display = 'block';
        button.appendChild(hideText);

        // Retourner le bouton
        return button;
    }

    /**
     * Méthode permettant de mettre en place les écouteurs d'événements dynamiques pour les boutons "Répondre" et "Signaler"
     * 
     * @details Cette méthode utilise la délégation d'événements pour les boutons "Répondre" et "Signaler" qui sont ajoutés dynamiquement, afin de remplir les données des messages originaux
     * 
     * @returns {void}
     */
    setupDynamicEventListeners() {
        const self = this;
        // Add event delegation for dynamically added messages
        document.addEventListener('click', function(event) {
            // Répondre dynamique
            const repondreButton = event.target.closest('.btn[data-bs-target="#repondreModal"]');
            if (repondreButton) {
                const repondreModal = document.getElementById("repondreModal");
                const modalTextarea = repondreModal.querySelector("textarea");
                const messageOriginal = repondreModal.querySelector(".message-original p");
                const auteurOriginal = repondreModal.querySelector(".message-original strong");
                const compteur = repondreModal.querySelector(".modal-body span");

                const parentMessage = repondreButton.closest(".card-body");
                const messageText = parentMessage.querySelector("p").textContent;
                const pseudoAuteur = parentMessage.querySelector(".username-link");

                messageOriginal.textContent = messageText.trim();
                auteurOriginal.textContent = pseudoAuteur.textContent.trim();
                modalTextarea.placeholder = `Répondre à ${pseudoAuteur.textContent.trim()}...`;
                modalTextarea.value = "";
                compteur.textContent = "0 / 1000";
            }

            // Signaler dynamique
            const signalerButton = event.target.closest('[data-bs-target="#signalement"]');
            if (signalerButton) {
                const messageId = signalerButton.getAttribute('data-id-message');
                const messageToSignal = document.querySelector(`.message[data-id-message="${messageId}"]`);
                const hiddenInputIdMessage = document.getElementById("id_message_signalement");

                if (messageToSignal) {
                    const messageText = messageToSignal.querySelector('.message-text').innerText;
                    document.querySelector('#signalement .message-to-signal').innerText = messageText;
                    hiddenInputIdMessage.value = messageId;
                } else {
                    console.log("Message Introuvable");
                }
            }
        });
    }
}