document.addEventListener("DOMContentLoaded", function () {
    const inputMot = document.getElementById("motInterdit");  // Champ de saisie
    const btnAjouter = document.getElementById("btnAjouter"); // Bouton "Ajouter"
    const btnSupprimer = document.getElementById("btnSupprimer"); // Bouton "Supprimer"

    // Liste des mots interdits récupérée dynamiquement
    let motsInterdits = [];

    // Récupérer la liste des mots interdits au chargement de la page
    fetch("index.php?controller=dashboard&methode=chargerMotsInterdits.php")
        .then(response => response.json())
        .then(data => {
            motsInterdits = data;
        })
        .catch(error => console.error("Erreur lors du chargement de la liste :", error));

    // Fonction pour envoyer une requête AJAX
    function envoyerRequete(action, mot) {
        fetch("index.php?controller=dashboard&methode=gererMotInterdit", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ action: action, mot: mot })
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message); // Affiche la réponse du serveur
            if (data.success) {
                if (action === "ajouter") motsInterdits.push(mot); // Met à jour la liste localement
                if (action === "supprimer") motsInterdits = motsInterdits.filter(m => m !== mot);
            }
        })
        .catch(error => console.error("Erreur AJAX :", error));
    }

    // Ajouter un mot interdit
    btnAjouter.addEventListener("click", function () {
        const mot = inputMot.value.trim().toLowerCase();

        if (!mot) {
            alert("Veuillez entrer un mot.");
            return;
        }

        if (motsInterdits.includes(mot)) {
            alert("Ce mot est déjà dans la liste des interdits.");
            return;
        }

        envoyerRequete("ajouter", mot);
    });

    // Supprimer un mot interdit
    btnSupprimer.addEventListener("click", function () {
        const mot = inputMot.value.trim().toLowerCase();

        if (!mot) {
            alert("Veuillez entrer un mot.");
            return;
        }

        if (!motsInterdits.includes(mot)) {
            alert("Ce mot n'est pas dans la liste.");
            return;
        }

        envoyerRequete("supprimer", mot);
    });
});
