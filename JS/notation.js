function generateStarRating(note, maxNote = 5) {
  const ratingContainer = document.getElementById("rating");

  if (!ratingContainer) {
    console.error("Conteneur #rating non trouvé");
    return;
  }

  // Nettoyage du conteneur
  ratingContainer.innerHTML = "";

  // Conteneur des étoiles
  const starContainer = document.createElement("div");
  starContainer.classList.add("star-rating"); // Ajout de la classe CSS pour le conteneur des étoiles

  // Génération des étoiles
  for (let i = 0; i < maxNote; i++) {
    const star = document.createElement("i");
    star.classList.add("fa", "fa-star"); // Utilisation des icônes Font Awesome
    if (i < Math.round(note)) {
      star.classList.add("stars-fill"); // Classe pour les étoiles remplies
    }
    starContainer.appendChild(star);
  }

  // Ajout au DOM
  ratingContainer.appendChild(starContainer);
}

// Debug global
console.log("Script de notation chargé");
