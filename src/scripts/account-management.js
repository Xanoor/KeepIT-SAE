document.querySelector('.eye-icon').addEventListener('click', function () {
    const passwordInput = document.getElementById('password');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        this.classList.add('visible');
    } else {
        passwordInput.type = 'password';
        this.classList.remove('visible');
    }
});

