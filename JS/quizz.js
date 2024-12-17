document.addEventListener("DOMContentLoaded", () => { // Récupération des données
    var champText = document.getElementById("desc");
    var counter = document.getElementById("compteur");
    const maxChar = 255;

    // Ajouter le compteur dynamique
    champText.addEventListener("input", () => { // Utilisation d'une fonction fléchée
        let currentLength = champText.value.length;
        let remaining = maxChar - currentLength;
        counter.textContent = `${remaining} caractères restants`; 
    });
});