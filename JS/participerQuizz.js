document.addEventListener('DOMContentLoaded', function() {
    //Variables
    const checkboxes = document.querySelectorAll('.checkbox'); //Récupère toutes les checkboxes
    
    //Ajoute un gestionnaire d'événement pour chaque checkbox
    checkboxes.forEach(checkbox => {
      checkbox.addEventListener('change', () => {
        let nbCheckboxesCochees = document.querySelectorAll('.checkbox:checked').length;
        
        //Empêche la dernière checkbox cochée de rester cochée
        if (nbCheckboxesCochees > 2) {
          checkbox.checked = false;
        }
      });
    });

});