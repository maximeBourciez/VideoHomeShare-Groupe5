document.addEventListener("DOMContentLoaded", function () {
  // Main page forum - Listing des threads
  const customSelect = document.getElementById("themeSelect");
  const placeholder = customSelect.querySelector(".custom-select-placeholder");
  const options = customSelect.querySelectorAll(".custom-select-option input");
  let errorThemeSpan = document.getElementById("errorTheme");   // Pour la prévention d'erreur à la création de thread


  // Ouverture du déroulant
  customSelect.addEventListener("click", function (e) {
    if (e.target.closest(".custom-select-dropdown") === null) {
      this.classList.toggle("open");
    }
  });

  // Récupérer les options sélectionnées
  options.forEach((option) => {
    option.addEventListener("change", function () {
      if (document.querySelectorAll(".custom-select-option input:checked").length > 3) {
        this.checked = false; // Désélectionne l'option si plus de 3 sont sélectionnées
        errorThemeSpan.classList.remove("d-none");
        errorThemeSpan.textContent = "Vous ne pouvez pas sélectionner plus de 3 thèmes.";
      }
      updateSelectedThemes();
    });
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
  const DescriptionThread = document.getElementById("descriptionThread");
  const compteurPremierMessage = document.getElementById("countPremierMsg");
  const compteurDescriptionThread = document.getElementById("countDescriptionThread");

  if (textareaPremMsg && compteurPremierMessage) {
    // Compteur pour le premier message
    textareaPremMsg.addEventListener("input", function () {
      compteurPremierMessage.textContent = `${textareaPremMsg.value.length} / 1000`;
    });
  } else {
    console.error("Element 'premierMessage' ou 'countPremierMsg' non trouvé !");
  }

  if (DescriptionThread && compteurDescriptionThread) {
    // Compteur pour le titre du thread
    DescriptionThread.addEventListener("input", function () {
      compteurDescriptionThread.textContent = `${DescriptionThread.value.length} / 100`;
    });
  } else {
    console.error(
      "Element 'descriptionThread' ou 'countDescriptionThread' non trouvé !"
    );
  }


  // Main page forum - Listing des threads - Prévention des erreurs à la création de thread
  let errorTitleSpan = document.getElementById("errorTitle");
  let errorDescription = document.getElementById("errorDescription");
  let errorPremierMessageSpan = document.getElementById("errorPremierMessage");
  let formCreationThread = document.getElementById("formCreationThread");
  let inputTitreThread = document.getElementById("titreThread");
  const MIN_LENGHT_INPUTS = 10;

  // Vérification du titre du thread
  formCreationThread.addEventListener("submit", function (e) {
    let hasError = false;
  
    // Vérification du titre du thread
    if (inputTitreThread.value.length < MIN_LENGHT_INPUTS || inputTitreThread.value.length > inputTitreThread.maxLength) {
      errorTitleSpan.classList.remove("d-none");
      errorTitleSpan.textContent = "Le titre doit contenir entre 10 et 50 caractères.";
      hasError = true;
    } else {
      errorTitleSpan.classList.add("d-none");
    }
  
    // Vérification de la description du thread
    if (DescriptionThread.value.length < MIN_LENGHT_INPUTS || DescriptionThread.value.length > DescriptionThread.maxLength) {
      errorDescription.classList.remove("d-none");
      errorDescription.textContent = "La description doit contenir entre 10 et 100 caractères.";
      hasError = true;
    } else {
      errorDescription.classList.add("d-none");
    }
  
    // Vérification du premier message
    if (textareaPremMsg.value.length < MIN_LENGHT_INPUTS || textareaPremMsg.value.length > textareaPremMsg.maxLength) {
      errorPremierMessageSpan.classList.remove("d-none");
      errorPremierMessageSpan.textContent = "Le premier message doit contenir entre 10 et 1000 caractères.";
      hasError = true;
    } else {
      errorPremierMessageSpan.classList.add("d-none");
    }
  
    if (hasError) {
      e.preventDefault();
    }
  });
  
});
