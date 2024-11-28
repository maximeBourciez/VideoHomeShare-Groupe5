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
  starContainer.style.display = "flex";
  starContainer.style.alignItems = "center";

  // Génération des étoiles
  for (let i = 0; i < maxNote; i++) {
    const star = document.createElement("span");
    star.textContent = "★";
    star.style.fontSize = "24px";
    star.style.color = i < Math.round(note) ? "#FFD700" : "#ddd";
    star.style.marginRight = "2px";
    starContainer.appendChild(star);
  }

  // Ajout au DOM
  ratingContainer.appendChild(starContainer);
}

// Debug global
console.log("Script de notation chargé");
