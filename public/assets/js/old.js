const btnMenu = document.querySelector('.btn-menu-responsive');
const btnClose = document.querySelector('.menu-mobile .btn-close');
const menuMobile = document.querySelector('.menu-mobile');

if (btnMenu && menuMobile) {
    btnMenu.addEventListener('click', () => {
        menuMobile.classList.add('active'); // Activa la animación de old.css
    });
}

if (btnClose && menuMobile) {
    btnClose.addEventListener('click', () => {
        menuMobile.classList.remove('active'); // Desactiva la animación de old.css
    });
}