document.addEventListener("DOMContentLoaded", function () {
  // Cacher le loader initialement
  const loader = document.querySelector(".loader-container");
  loader.style.display = "none";

  // Fonction pour afficher le loader
  function showLoader(event) {
    // Ne pas afficher le loader pour les modals et les liens internes
    const target = event.currentTarget;
    const isModal = target.getAttribute("data-bs-toggle") === "modal";
    const isInternalLink = target.getAttribute("href")?.startsWith("#");
    const isInModal = target.closest(".modal") !== null;

    if (!isModal && !isInternalLink && !isInModal) {
      loader.style.display = "flex";
    }
  }

  // Ajouter les gestionnaires d'événements
  const links = document.querySelectorAll('a:not([data-bs-toggle="modal"])');
  const buttons = document.querySelectorAll(
    'button[type="submit"]:not([data-bs-toggle="modal"])'
  );
  const forms = document.querySelectorAll("form:not([data-modal-form])");

  // Pour les liens
  links.forEach((link) => {
    link.addEventListener("click", showLoader);
  });

  // Pour les boutons
  buttons.forEach((button) => {
    button.addEventListener("click", showLoader);
  });

  // Pour les formulaires
  forms.forEach((form) => {
    form.addEventListener("submit", function (e) {
      // Vérification des champs textarea
      const textareas = this.querySelectorAll("textarea[required]");
      let isValid = true;

      textareas.forEach((textarea) => {
        const minLength = textarea.getAttribute("minlength") || 10; // Par défaut 10 caractères
        if (textarea.value.length < minLength) {
          isValid = false;
          textarea.classList.add("is-invalid");
          e.preventDefault();
        } else {
          textarea.classList.remove("is-invalid");
        }
      });

      // Vérification des inputs requis
      const inputs = this.querySelectorAll("input[required]");
      inputs.forEach((input) => {
        if (!input.value.trim()) {
          isValid = false;
          input.classList.add("is-invalid");
          e.preventDefault();
        } else {
          input.classList.remove("is-invalid");
        }
      });

      // Afficher le loader uniquement si le formulaire est valide
      if (isValid && this.checkValidity()) {
        loader.style.display = "flex";
      } else {
        loader.style.display = "none";
      }
    });
  });

  // Cacher le loader une fois la page chargée
  window.addEventListener("load", function () {
    loader.style.display = "none";
  });

  // Gestionnaire d'erreurs global
  window.addEventListener("error", function () {
    loader.style.display = "none";
  });

  // Gestion des modals
  const modals = document.querySelectorAll(".modal");
  modals.forEach((modal) => {
    modal.addEventListener("hidden.bs.modal", function () {
      loader.style.display = "none";
    });
  });
});
