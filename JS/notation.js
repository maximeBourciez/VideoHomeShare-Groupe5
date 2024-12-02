function generateStarRating(note, maxNote = 5) {
  const ratingContainer = document.getElementById("rating");
  if (!ratingContainer) {
    console.error("Conteneur #rating non trouvé");
    return;
  }
  
  ratingContainer.innerHTML = "";
  
  const starContainer = document.createElement("div");
  starContainer.classList.add("star-rating");
  
  for (let i = 0; i < maxNote; i++) {
    const starWrapper = document.createElement("span");
    starWrapper.classList.add("star-wrapper");
    
    const starFull = document.createElement("i");
    starFull.classList.add("fa", "fa-star", "star-full");
    
    if (i < Math.floor(note)) {
      // Étoiles pleines
      starWrapper.classList.add("stars-fill");
      starWrapper.appendChild(starFull);
    } else if (i === Math.floor(note) && note % 1 >= 0.5) {
      // Demi-étoile
      const starHalf = document.createElement("i");
      starHalf.classList.add("fa", "fa-star-half-o", "star-half");
      
      starWrapper.classList.add("stars-half");
      starWrapper.appendChild(starFull);
      starWrapper.appendChild(starHalf);
    } else {
      // Étoiles vides
      starFull.classList.add("star-empty");
      starWrapper.appendChild(starFull);
    }
    
    starContainer.appendChild(starWrapper);
  }
  
  ratingContainer.appendChild(starContainer);
}