document.addEventListener('DOMContentLoaded', () => {
    const containerProductos = document.getElementById('product-list-coleccion');
    const searchInput = document.getElementById('search-input');
    const categoryButtons = document.querySelectorAll('.category-buttons button');

    const btnAnterior = document.getElementById('btnAnterior');
    const btnSiguiente = document.getElementById('btnSiguiente');
    const paginaActualSpan = document.getElementById('paginaActual');

    const productosPorPagina = 12;
    let paginaActual = 1;
    let totalPaginas = 1;
    let productosGlobal = [];

    // Función para cargar productos desde AJAX
    async function cargarProductos(busqueda = '', categoria = '') {
        try {
            const res = await fetch(`/inge/pages/ajax/buscarProductos.php?q=${encodeURIComponent(busqueda)}&categoria=${encodeURIComponent(categoria)}`);
            const data = await res.json();
            productosGlobal = data || [];

            totalPaginas = Math.ceil(productosGlobal.length / productosPorPagina);
            paginaActual = 1;

            renderizarPagina();
        } catch (err) {
            console.error('Error cargando productos:', err);
            containerProductos.innerHTML = '<p class="no-results">Error cargando productos.</p>';
        }
    }

    // Función para renderizar la página actual
    function renderizarPagina() {
        containerProductos.innerHTML = '';

        if (!productosGlobal || productosGlobal.length === 0) {
            containerProductos.innerHTML = '<p class="no-results">No hay productos disponibles.</p>';
            paginaActualSpan.textContent = '';
            return;
        }

        const inicio = (paginaActual - 1) * productosPorPagina;
        const fin = inicio + productosPorPagina;
        const productosPagina = productosGlobal.slice(inicio, fin);

        productosPagina.forEach(prod => {
            const card = document.createElement('div');
            card.className = 'product-card';

            card.innerHTML = `
                <img src="${prod.imagen_url || 'img/placeholder.jpg'}" alt="${prod.nombre}">
                <h3>${prod.nombre}</h3>
                <p>${prod.descripcion || ''}</p>
                <span class="price">$${parseFloat(prod.precio).toFixed(2)}</span>
                <p>Stock: ${prod.cantidad}</p>
            `;

            // Botón Ver Detalles
            const btnDetalles = document.createElement('button');
            btnDetalles.textContent = 'Ver detalles';
            btnDetalles.className = 'btn-detalles';
            btnDetalles.addEventListener('click', () => {
                window.location.href = `/inge/pages/detallesProducto.php?id=${prod.id}`;
            });
            card.appendChild(btnDetalles);

            // Botones admin
            if (window.usuarioTipo === 'adm') {
                const acciones = document.createElement('div');
                acciones.className = 'product-actions';

                const btnEditar = document.createElement('button');
                btnEditar.textContent = 'Editar';
                btnEditar.className = 'btn-editar';
                btnEditar.addEventListener('click', () => {
                    window.location.href = `/inge/pages/registroProducto.php?id=${prod.id}`;
                });

                const btnEliminar = document.createElement('button');
                btnEliminar.textContent = 'Eliminar';
                btnEliminar.className = 'btn-eliminar';
                btnEliminar.addEventListener('click', () => window.eliminarProducto(prod.id));

                acciones.appendChild(btnEditar);
                acciones.appendChild(btnEliminar);
                card.appendChild(acciones);
            }

            containerProductos.appendChild(card);
        });

        paginaActualSpan.textContent = `Página ${paginaActual} de ${totalPaginas}`;
    }

    // Eventos de paginación
    btnAnterior.addEventListener('click', () => {
        if (paginaActual > 1) {
            paginaActual--;
            renderizarPagina();
        }
    });

    btnSiguiente.addEventListener('click', () => {
        if (paginaActual < totalPaginas) {
            paginaActual++;
            renderizarPagina();
        }
    });

    // Buscar mientras escribes
    searchInput.addEventListener('input', () => {
        cargarProductos(searchInput.value, getCategoriaActiva());
    });

    // Filtrar por categoría
    categoryButtons.forEach(btn => {
        btn.addEventListener('click', () => {
            categoryButtons.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            cargarProductos(searchInput.value, btn.getAttribute('data-category'));
        });
    });

    // Obtener categoría activa
    function getCategoriaActiva() {
        const activeBtn = document.querySelector('.category-buttons button.active');
        return activeBtn ? activeBtn.getAttribute('data-category') : '';
    }

    // Cargar todos los productos inicialmente
    cargarProductos();
});