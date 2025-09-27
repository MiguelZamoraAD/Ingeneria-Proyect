//Se usara para la alguna logica a futuro
document.addEventListener('DOMContentLoaded', () => {
    const productList = document.getElementById('product-list');
    const categoryButtons = document.querySelectorAll('.category-buttons button');
    const searchInput = document.getElementById('search-input');
    const cartCountSpan = document.getElementById('cart-count');
    const profileLink = document.getElementById('profile-link');
    const profileMenu = document.getElementById('profile-menu');

    let cartCount = 0;

    const renderProducts = (productsToRender) => {
        productList.innerHTML = '';
        if (productsToRender.length === 0) {
            productList.innerHTML = '<p style="text-align: center;">No se encontraron productos.</p>';
            return;
        }
        productsToRender.forEach(product => {
            const productCard = document.createElement('div');
            productCard.classList.add('product-card');
            productCard.innerHTML = `
                <img src="assets/images/${product.image}" alt="${product.name}">
                <h3>${product.name}</h3>
                <p>${product.description}</p>
                <p class="price">$${product.price.toFixed(2)}</p>
                <button class="buy-button" data-product-id="${product.id}">Añadir al Carrito</button>
            `;
            productList.appendChild(productCard);
        });
    };

    categoryButtons.forEach(button => {
        button.addEventListener('click', () => {
            categoryButtons.forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            const category = button.dataset.category;
            const filteredProducts = category === 'todos' ? products : products.filter(product => product.category === category);
            renderProducts(filteredProducts);
        });
    });

    const performSearch = () => {
        const query = searchInput.value.toLowerCase();
        const filteredProducts = products.filter(product =>
            product.name.toLowerCase().includes(query) ||
            product.description.toLowerCase().includes(query) ||
            product.category.toLowerCase().includes(query)
        );
        renderProducts(filteredProducts);
    };

    searchInput.addEventListener('input', performSearch);
    document.getElementById('search-button').addEventListener('click', (e) => {
        e.preventDefault();
        performSearch();
    });

    productList.addEventListener('click', (e) => {
        if (e.target.classList.contains('buy-button')) {
            const productId = e.target.dataset.productId;
            const productToAdd = products.find(p => p.id === parseInt(productId));
            if (productToAdd) {
                cartCount++;
                cartCountSpan.textContent = cartCount;
                alert(`${productToAdd.name} ha sido añadido al carrito.`);
            }
        }
    });

    profileLink.addEventListener('click', (e) => {
        e.preventDefault();
        profileMenu.style.display = profileMenu.style.display === 'block' ? 'none' : 'block';
    });

    window.addEventListener('click', (e) => {
        if (!e.target.matches('#profile-link') && !profileMenu.contains(e.target)) {
            profileMenu.style.display = 'none';
        }
    });

});