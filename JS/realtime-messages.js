class RealtimeMessages {
    constructor(idFil) {
        this.idFil = idFil;
        this.dernierMessageId = this.getDernierMessageId();
        this.interval = null;
        this.initializePolling();
    }

    getDernierMessageId() {
        const messages = document.querySelectorAll('.message');
        if (messages.length === 0) return 0;
        
        const ids = Array.from(messages).map(msg => 
            parseInt(msg.dataset.idMessage)
        );
        return Math.max(...ids);
    }

    initializePolling() {
        // Vérifie les nouveaux messages toutes les 5 secondes
        this.interval = setInterval(() => this.verifierNouveauxMessages(), 5000);
    }

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

    ajouterNouveauxMessages(messages) {
        const container = document.querySelector('.container.mt-3');
        
        for(let i = 0 ; i < messages.length ; i++) {
            var message = messages[i];
            if (!document.querySelector(`[data-id-message="${message.idMessage}"]`)) {
                const messageHTML = this.creerMessageHTML(message);
                
                if (message.idMessageParent) {
                    // C'est une réponse
                    const parentContainer = document.querySelector(`[data-id-message="${message.idMessageParent}"] .responses-container`);
                    if (parentContainer) {
                        parentContainer.insertAdjacentHTML('beforeend', messageHTML);
                    }
                    else {
                        console.error('Impossible de trouver le conteneur parent pour le message:', message);
                    }
                } else {
                    // C'est un message principal
                    container.insertAdjacentHTML('beforeend', messageHTML);
                }
            }
        };
    }

    creerMessageHTML(message) {
        // Decode HTML entities
        const decodedMessage = message.valeur
            .replace(/&#039;/g, "'")
            .replace(/\r\n/g, '<br>');
    
        return `
            <div id="${message.idMessage}" class="card my-4 shadow-sm border-0 rounded-3 ${message.idMessageParent ? 'ms-md-5 ms-2' : ''}">
                <div class="card-body p-4 bg-mydark text-white rounded">
                    <div class="d-flex align-items-center">
                        <img src="${message.urlImageProfil || 'default_avatar.jpg'}" alt="Avatar" class="rounded-circle me-3" width="50" height="50">
                        <div class="d-flex align-items-center">
                            <h5 class="mb-0 mx-2 username-link">
                                <a href="index.php?controller=utilisateur&methode=show&id_utilisateur=${message.idUtilisateur}" style="color:white;" class="text-decoration-none">
                                    ${message.pseudo || 'Utilisateur inconnu'}
                                </a>
                            </h5>
                            <span class="mx-2" style="font-size: 1.2em;">•</span>
                            <small>${new Date(message.dateC).toLocaleDateString()}</small>
                        </div>
                    </div>
                    <div class="message" data-id-message="${message.idMessage}">
                        <p class="mt-3 message-text">${decodedMessage}</p>
                    </div>
                </div>
            </div>
        `;
    }
} 
