document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('registrationForm');
    const passwordInput = document.getElementById('mdp');
    const confirmPasswordInput = document.getElementById('vmdp');
    const togglePasswordBtn = document.getElementById('togglePassword');
    
    // Password toggle visibility
    togglePasswordBtn.addEventListener('click', function () {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        confirmPasswordInput.setAttribute('type', type);
    });

    // Form validation
    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);

    // Password RegEx validation
    passwordInput.addEventListener('input', function() {
        const regex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
        
        if (!regex.test(passwordInput.value)) {
            passwordInput.setCustomValidity('Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.');
            // Force immediate feedback
            form.classList.add('was-validated');
        } else {
            passwordInput.setCustomValidity('');
        }
        
        // Check password match again when the password changes
        if (confirmPasswordInput.value) {
            if (passwordInput.value !== confirmPasswordInput.value) {
                confirmPasswordInput.setCustomValidity('Les mots de passe ne correspondent pas');
            } else {
                confirmPasswordInput.setCustomValidity('');
            }
        }
    });

    // Password match validation
    confirmPasswordInput.addEventListener('input', function() {
        if (passwordInput.value !== confirmPasswordInput.value) {
            confirmPasswordInput.setCustomValidity('Les mots de passe ne correspondent pas');
        } else {
            confirmPasswordInput.setCustomValidity('');
        }
        
        // Force immediate feedback
        form.classList.add('was-validated');
    });

    // Add debug to check if messages are being set
    console.log('Script initialized successfully');
});