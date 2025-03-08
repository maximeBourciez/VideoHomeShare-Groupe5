document.addEventListener('DOMContentLoaded', function() {
    //Variables
    const checkboxes = document.querySelectorAll('.checkbox'); //Récupère toutes les checkboxes
    
    //Ajoute un gestionnaire d'événement pour chaque checkbox
    checkboxes.forEach(checkbox => {
      const label = checkbox.parentElement; //Récupère le label associé au checkbox
      checkbox.addEventListener('change', function(){
        let nbCheckboxesCochees = document.querySelectorAll('.checkbox:checked').length;
        
        //Empêche la dernière checkbox cochée de rester cochée
        if (nbCheckboxesCochees > 2) {
          this.checked = false;
        }

        if (checkbox.checked){
          label.classList = 'btn btn-primary';        
        }
        else{
          label.classList = 'btn btn-outline-tertiary';            
        }
      });
    });

});