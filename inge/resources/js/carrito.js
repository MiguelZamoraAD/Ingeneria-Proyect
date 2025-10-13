document.addEventListener('DOMContentLoaded', () => {
    const addToCartBtn = document.querySelector('.add-to-cart-button');
    const cartCount = document.getElementById('cart-count');

    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', () => {
            const productId = new URLSearchParams(window.location.search).get('id');

            fetch('../func/funCarrito.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${encodeURIComponent(productId)}`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        cartCount.textContent = data.total; // actualiza número del carrito
                        addToCartBtn.textContent = '✅ Agregado!';
                        addToCartBtn.disabled = true;
                        setTimeout(() => {
                            addToCartBtn.textContent = 'Agregar al carrito';
                            addToCartBtn.disabled = false;
                        }, 1000);
                    } else {
                        alert('Error al agregar: ' + data.msg);
                    }
                })
                .catch(err => console.error('Error:', err));
        });
    }
});