//Se usara para la alguna logica a futuro
document.addEventListener('DOMContentLoaded', () => {
    const products = [{
        id: 1,
        name: 'Guitarra Eléctrica Vintage',
        description: 'Sonido clásico, perfecto para rock y blues.',
        price: 499.99,
        image: 'guitarra1.jpg',
        category: 'guitarras'
    }, {
        id: 2,
        name: 'Batería Acústica Completa',
        description: 'Kit completo, ideal para principiantes y estudios.',
        price: 899.99,
        image: 'bateria1.jpg',
        category: 'baterias'
    }, {
        id: 3,
        name: 'Teclado Electrónico 61 Teclas',
        description: 'Variedad de sonidos y ritmos, ideal para aprender.',
        price: 249.99,
        image: 'teclado1.jpg',
        category: 'teclados'
    }, {
        id: 4,
        name: 'Micrófono de Condensador USB',
        description: 'Captura de voz de alta calidad para grabación.',
        price: 129.99,
        image: 'microfono.jpg',
        category: 'accesorios'
    }, {
        id: 5,
        name: 'Bajo Eléctrico de 4 Cuerdas',
        description: 'Graves potentes y un diseño elegante.',
        price: 349.99,
        image: 'bajo.jpg',
        category: 'guitarras'
    }, {
        id: 6,
        name: 'Pedal de Distorsión',
        description: 'Añade un toque de rock a tu sonido de guitarra.',
        price: 79.99,
        image: 'pedal.jpg',
        category: 'accesorios'
    }, {
        id: 7,
        name: 'Ukulele de Concierto',
        description: 'Instrumento divertido y fácil de aprender.',
        price: 59.99,
        image: 'ukulele.jpg',
        category: 'guitarras'
    }, {
        id: 8,
        name: 'Set de Platillos de Jazz',
        description: 'Platillos con sonido cálido y rico.',
        price: 299.99,
        image: 'platillos.jpg',
        category: 'baterias'
    }, ];

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

    renderProducts(products);
});