function generateStarRating(note, maxNote = 5) {
  // Sélectionner tous les conteneurs de notation qui n'ont pas encore été traités
  const ratingContainers = document.querySelectorAll(
    ".rating-container:not(.processed)"
  );

  ratingContainers.forEach((container) => {
    const ratingElement = container.querySelector("#rating");
    if (!ratingElement) {
      console.error("Élément #rating non trouvé dans le conteneur");
      return;
    }

    ratingElement.innerHTML = "";
    const starContainer = document.createElement("div");
    starContainer.classList.add("star-rating");

    for (let i = 0; i < maxNote; i++) {
      const starWrapper = document.createElement("span");
      starWrapper.classList.add("star-wrapper");
      const starFull = document.createElement("i");
      starFull.classList.add("fa", "fa-star", "star-full");

      if (i < Math.floor(note)) {
        starWrapper.classList.add("stars-fill");
        starWrapper.appendChild(starFull);
      } else if (i === Math.floor(note) && note % 1 >= 0.5) {
        const starHalf = document.createElement("i");
        starHalf.classList.add("fa", "fa-star-half-o", "star-half");
        starWrapper.classList.add("stars-half");
        starWrapper.appendChild(starFull);
        starWrapper.appendChild(starHalf);
      } else {
        starFull.classList.add("star-empty");
        starWrapper.appendChild(starFull);
      }
      starContainer.appendChild(starWrapper);
    }

    ratingElement.appendChild(starContainer);
    // Marquer ce conteneur comme traité
    container.classList.add("processed");
  });
}
