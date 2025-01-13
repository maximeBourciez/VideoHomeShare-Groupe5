document.addEventListener('DOMContentLoaded', function() {
    let nbReponses = 0; // Nombre de réponses ajoutées
    const maxResponses = 4; // Limite de réponses

    // Fonction pour ajouter une nouvelle réponse
    document.getElementById('addReponseBtn').addEventListener('click', function() {
        if (nbReponses >= maxResponses){
            alert("Vous avez atteint la limite de réponses !");
            return;
        }

        nbReponses++; // Incrémenter le compteur des réponses

        // Créer un nouvel élément pour la réponse
        let zoneReponse = document.createElement('div');
        zoneReponse.classList.add('card', 'mb-3');
        zoneReponse.innerHTML = `
        <div class="card">
            <div class="card-body">
                <label for="reponse_${nbReponses}">Réponse n°${nbReponses} :</label>
                <input type="text" name="reponse_${nbReponses}" id="reponse_${nbReponses}" placeholder="Saisir la réponse..." class="form-control" required>
                <br>
                <label for="veriteReponse">
                    <input type="radio" id="veriteReponse" name="veriteReponse" value="no">
                    Réponse correcte
                </label>
            </div>
        </div>
        `;

        // Ajouter la nouvelle réponse dans le container de réponses
        document.getElementById('reponsesContainer').appendChild(zoneReponse);
    });
});