document.addEventListener('DOMContentLoaded', () => {
    const profileLink = document.getElementById('profile-link');
    const profileMenu = document.getElementById('profile-menu');
    const cartCountSpan = document.getElementById('cart-count');
    let cartCount = 0; // Este valor debería ser cargado dinámicamente en una aplicación real.

    // Lógica para mostrar/ocultar el menú del perfil
    profileLink.addEventListener('click', (e) => {
        e.preventDefault();
        profileMenu.style.display = profileMenu.style.display === 'block' ? 'none' : 'block';
    });

    // Ocultar el menú al hacer clic fuera de él
    window.addEventListener('click', (e) => {
        if (!e.target.matches('#profile-link') && !profileMenu.contains(e.target)) {
            profileMenu.style.display = 'none';
        }
    });

    // En una aplicación real, aquí se cargaría el número de elementos en el carrito
    // cartCountSpan.textContent = algún_valor_real;
});