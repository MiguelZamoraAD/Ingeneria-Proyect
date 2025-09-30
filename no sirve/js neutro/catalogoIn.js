const supabaseUrl = 'https://recghdynvcvyzdrtmouj.supabase.co';
const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InJlY2doZHludmN2eXpkcnRtb3VqIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTc1NTU4MzcsImV4cCI6MjA3MzEzMTgzN30.l7O6l_P3k0TinXjRbj9v6EN0x6iXzLxcuQEUqVtyfdE'; // Usa la clave anónima pública
const supabase = window.supabase.createClient(supabaseUrl, supabaseKey);

document.addEventListener('DOMContentLoaded', () => {
    let editandoId = null;

    // ==== CRUD auxiliares ====
    window.eliminarProducto = id => {
        const fd = new FormData();
        fd.append('Accion', 'Eliminar');
        fd.append('id', id);
        fetch('../func/procesarproducto.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Eliminado', 'Producto eliminado', 'success');
                    cargarProductos();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
    };

    window.obtenerProducto = id => {
        const fd = new FormData();
        fd.append('Accion', 'Obtener');
        fd.append('id', id);
        fetch('../func/procesarproducto.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.success && data.producto) {
                    const p = data.producto;
                    document.getElementById('nombre').value = p.nombre;
                    document.getElementById('descripcion').value = p.descripcion;
                    document.getElementById('precio').value = p.precio;
                    document.getElementById('cantidad').value = p.cantidad;
                    document.getElementById('artista').value = p.artista_id || '';
                    document.getElementById('categoria').value = p.categoria_id || '';
                    editandoId = p.id;
                    document.querySelector('.registro-header h2').textContent = 'Actualizar Producto';
                    document.querySelector('.btnRegistrar').textContent = 'Actualizar';
                } else {
                    Swal.fire('Error', data.message || 'No se pudo cargar', 'error');
                }
            });
    };

    window.cargarProductos = (pagina = 1) => {
        const fd = new FormData();
        fd.append('Accion', 'Listar');
        fd.append('pagina', pagina);
        fetch('../func/procesarproducto.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    console.table(data.data);
                }
            });
    };

    // ==== Cargar productos colección ====
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
                    <p>${prod.descripcion || ''}</p>
                    <span class="price">$${parseFloat(prod.precio).toFixed(2)}</span>
                `;
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
                        window.location.href = `pages/registroProducto.php?id=${prod.id}`;
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

    // ==== INICIAL ====
    cargarProductosColeccion();
});