document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.querySelector('.menu-toggle');
    const userMenuToggle = document.querySelector('.user-menu-toggle');
    const navList = document.querySelector('.nav-list');
    const navMenu = document.querySelector('.nav-menu ul');

    if (menuToggle && navList) {
        menuToggle.addEventListener('click', () => {
            navList.classList.toggle('active');
        });
    }

    if (userMenuToggle && navMenu) {
        userMenuToggle.addEventListener('click', () => {
            navMenu.classList.toggle('active');
        });
    }
});