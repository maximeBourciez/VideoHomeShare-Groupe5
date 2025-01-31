document.addEventListener("DOMContentLoaded", function () {
  // Mise à jour de la valeur du slider
  const slider = document.getElementById("note");
  const sliderValue = document.getElementById("sliderValue");
  if (slider && sliderValue) {
    slider.addEventListener("input", () => {
      sliderValue.textContent = slider.value;
    });
  }

  // Validation du formulaire
  const form = document.getElementById("avisForm");
  const titreInput = document.getElementById("titre");
  const commentaireInput = document.getElementById("commentaire");

  if (form && titreInput && commentaireInput) {
    form.addEventListener("submit", function (event) {
      let isValid = true;

      // Réinitialiser les états de validation
      titreInput.classList.remove("is-invalid");
      commentaireInput.classList.remove("is-invalid");

      // Vérifier la longueur du titre
      if (titreInput.value.length < 3) {
        titreInput.classList.add("is-invalid");
        isValid = false;
      }

      // Vérifier la longueur du commentaire
      if (commentaireInput.value.length < 10) {
        commentaireInput.classList.add("is-invalid");
        isValid = false;
      }

      // Si le formulaire n'est pas valide, empêcher l'envoi
      if (!isValid) {
        event.preventDefault();
      }
    });

    // Validation en temps réel
    titreInput.addEventListener("input", function () {
      if (this.value.length < 3) {
        this.classList.add("is-invalid");
      } else {
        this.classList.remove("is-invalid");
      }
    });

    commentaireInput.addEventListener("input", function () {
      if (this.value.length < 10) {
        this.classList.add("is-invalid");
      } else {
        this.classList.remove("is-invalid");
      }
    });
  }
});
