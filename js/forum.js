document.addEventListener("DOMContentLoaded", function () {
  console.log("DOM chargé !");

  // Main page forum - Listing des threads
  const customSelect = document.getElementById("themeSelect");
  const placeholder = customSelect.querySelector(".custom-select-placeholder");
  const options = customSelect.querySelectorAll(".custom-select-option input");

  // Ouverture du déroulant
  customSelect.addEventListener("click", function (e) {
    if (e.target.closest(".custom-select-dropdown") === null) {
      this.classList.toggle("open");
    }
  });

  // Récupérer les options sélectionnées
  options.forEach((option) => {
    option.addEventListener("change", updateSelectedThemes);
  });

  function updateSelectedThemes() {
    const selectedOptions = Array.from(options)
      .filter((option) => option.checked)
      .map((option) => option.parentElement.textContent.trim());

    placeholder.textContent =
      selectedOptions.length > 0
        ? selectedOptions.join(", ")
        : "Choisissez des thèmes";
  }

  // Fermer le déroulant si on clique en dehors
  document.addEventListener("click", function (e) {
    if (!customSelect.contains(e.target)) {
      customSelect.classList.remove("open");
    }
  });

  // Main page forum - Listing des threads - Compteurs dans pop-up création de thread
  const textareaPremMsg = document.getElementById("premierMessage");
  const titreThread = document.getElementById("descriptionThread");
  const compteurPremierMessage = document.getElementById("countPremierMsg");
  const compteurTitreThread = document.getElementById("countDescriptionThread");

  if (textareaPremMsg && compteurPremierMessage) {
    // Compteur pour le premier message
    textareaPremMsg.addEventListener("input", function () {
      compteurPremierMessage.textContent = `${textareaPremMsg.value.length} / 1000`;
    });
  } else {
    console.error("Element 'premierMessage' ou 'countPremierMsg' non trouvé !");
  }

  if (titreThread && compteurTitreThread) {
    // Compteur pour le titre du thread
    titreThread.addEventListener("input", function () {
      compteurTitreThread.textContent = `${titreThread.value.length} / 100`;
    });
  } else {
    console.error("Element 'descriptionThread' ou 'countTitreThread' non trouvé !");
  }
});
