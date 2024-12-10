document.addEventListener('DOMContentLoaded', function() {
    // Main page forum - Listing des threads
    const customSelect = document.getElementById('themeSelect');
    const placeholder = customSelect.querySelector('.custom-select-placeholder');
    const options = customSelect.querySelectorAll('.custom-select-option input');
  
    // Toggle dropdown
    customSelect.addEventListener('click', function(e) {
      if (e.target.closest('.custom-select-dropdown') === null) {
        this.classList.toggle('open');
      }
    });
  
    // Handle checkbox selections
    options.forEach(option => {
      option.addEventListener('change', updateSelectedThemes);
    });
  
    function updateSelectedThemes() {
      const selectedOptions = Array.from(options)
        .filter(option => option.checked)
        .map(option => option.parentElement.textContent.trim());
  
      placeholder.textContent = selectedOptions.length > 0 
        ? selectedOptions.join(', ')
        : 'Choisissez des thèmes';
    }
  
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (!customSelect.contains(e.target)) {
        customSelect.classList.remove('open');
      }
    });
});
