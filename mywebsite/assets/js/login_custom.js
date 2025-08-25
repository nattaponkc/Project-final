// Custom JavaScript for Login Page

document.addEventListener('DOMContentLoaded', () => {
    const loginButton = document.querySelector('.btn-login');

    loginButton.addEventListener('click', () => {
        loginButton.classList.add('clicked');
        setTimeout(() => {
            loginButton.classList.remove('clicked');
        }, 300);
    });
});
