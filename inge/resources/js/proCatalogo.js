document.addEventListener('DOMContentLoaded', () => {

    // ==== Eliminar ====
    window.eliminarProducto = id => {
        // Confirmación antes de eliminar
        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡No podrás revertir esto!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                const fd = new FormData();
                fd.append('Accion', 'Eliminar');
                fd.append('id', id);

                fetch('/inge/func/procesarproducto.php', { method: 'POST', body: fd })
                    .then(async r => {
                        try {
                            return await r.json();
                        } catch (err) {
                            throw new Error('La respuesta del servidor no es JSON válido');
                        }
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Eliminado', 'Producto eliminado correctamente', 'success');
                            cargarProductosColeccion(); // recarga productos
                        } else {
                            Swal.fire('Error', data.message || 'No se pudo eliminar', 'error');
                        }
                    })
                    .catch(err => {
                        console.error('Error en eliminarProducto:', err);
                        Swal.fire('Error', 'Hubo un problema de conexión o de respuesta del servidor', 'error');
                    });
            }
        });
    };


    async function cargarProductosColeccion() {
        try {
            // 1. Traer todos los productos desde la tabla "producto"
            const { data: productos, error } = await supabase
                .from('producto')
                .select('*')
                .order('creado_en', { ascending: false });

            if (error) {
                console.error('Error al cargar productos:', error);
                return;
            }

            const container = document.getElementById('product-list-coleccion');
            if (!container) return;
            container.innerHTML = '';

            if (!productos || productos.length === 0) {
                container.innerHTML = '<p>No hay productos disponibles en este momento.</p>';
                return;
            }

            // 2. Renderizar productos en el grid
            productos.forEach(prod => {
                const card = document.createElement('div');
                card.className = 'product-card';
                card.innerHTML = `
                    <img src="${prod.imagen_url || 'img/placeholder.jpg'}"
                         alt="${prod.nombre}">
                    <h3>${prod.nombre}</h3>
                    <p>${cortarContenido(prod.descripcion) || ''}</p>
                    <span class="price">$${parseFloat(prod.precio).toFixed(2)}</span>
                    <p>Stock: ${prod.cantidad}</p>
                    
                `;

                // === Botón Ver Detalles (para todos) ===
                const btnDetalles = document.createElement('button');
                btnDetalles.textContent = 'Ver detalles';
                btnDetalles.className = 'btn-detalles';
                btnDetalles.addEventListener('click', () => {
                    window.location.href = `/inge/pages/detallesProducto.php?id=${prod.id}`;
                });

                card.appendChild(btnDetalles);

                // Solo agregar botones si es admin
                const esAdmin = window.usuarioTipo === 'adm'; // por ejemplo, puedes setear esto al cargar la página
                if (esAdmin) {
                    const acciones = document.createElement('div');
                    acciones.className = 'product-actions';

                    // Botón Editar: redirige al formulario con id en query string
                    const btnEditar = document.createElement('button');
                    btnEditar.textContent = 'Editar';
                    btnEditar.className = 'btn-editar';
                    btnEditar.addEventListener('click', () => {
                        window.location.href = `/inge/pages/registroProducto.php?id=${prod.id}`;
                    });

                    // Botón Eliminar: llama a tu función existente
                    const btnEliminar = document.createElement('button');
                    btnEliminar.textContent = 'Eliminar';
                    btnEliminar.className = 'btn-eliminar';
                    btnEliminar.addEventListener('click', () => eliminarProducto(prod.id));

                    acciones.appendChild(btnEditar);
                    acciones.appendChild(btnEliminar);
                    card.appendChild(acciones);
                }
                container.appendChild(card);

            });
        } catch (err) {
            console.error('Error general:', err);
        }
    }

    function cortarContenido(texto, max = 15) {
        if (texto.length <= max) return texto;
        const corte = texto.indexOf(" ", max);
        return texto.substring(0, corte !== -1 ? corte : max) + "...";
    }

    // ==== INICIAL ====
    cargarProductosColeccion();
});