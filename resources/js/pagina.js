// paginacion.js
document.addEventListener('DOMContentLoaded', () => {
    const containerPaginacion = document.querySelector('.paginacion-usuarios');
    const containerProductos = document.getElementById('product-list-coleccion');

    if (!containerPaginacion || !containerProductos) return; // solo ejecuta si existen los elementos

    const btnAnterior = document.getElementById('btnAnterior');
    const btnSiguiente = document.getElementById('btnSiguiente');
    const paginaActualSpan = document.getElementById('paginaActual');

    const productosPorPagina = 12;
    let paginaActual = 1;
    let totalPaginas = 1;
    let productosGlobal = [];

    async function cargarProductosPaginados() {
        try {
            const { data: productos, error } = await supabase
                .from('producto')
                .select('*')
                .order('creado_en', { ascending: false });

            if (error) {
                console.error('Error al cargar productos:', error);
                return;
            }

            productosGlobal = productos || [];
            totalPaginas = Math.ceil(productosGlobal.length / productosPorPagina);
            paginaActual = Math.min(paginaActual, totalPaginas);

            renderizarPagina();
        } catch (err) {
            console.error('Error general en paginación:', err);
        }
    }

    function renderizarPagina() {
        containerProductos.innerHTML = '';

        if (!productosGlobal || productosGlobal.length === 0) {
            containerProductos.innerHTML = '<p>No hay productos disponibles.</p>';
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
            `;

            const esAdmin = window.usuarioTipo === 'adm';
            if (esAdmin) {
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

    // Ejecutar la carga inicial
    cargarProductosPaginados();
});