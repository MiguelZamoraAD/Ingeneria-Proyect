const supabaseUrl = 'https://recghdynvcvyzdrtmouj.supabase.co';
const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InJlY2doZHludmN2eXpkcnRtb3VqIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTc1NTU4MzcsImV4cCI6MjA3MzEzMTgzN30.l7O6l_P3k0TinXjRbj9v6EN0x6iXzLxcuQEUqVtyfdE'; // Usa la clave anónima pública
const supabase = window.supabase.createClient(supabaseUrl, supabaseKey);

document.addEventListener('DOMContentLoaded', () => {
    const formCrear = document.getElementById('productoForm');
    const inputImagen = document.getElementById('imagen');
    const previewImg = document.getElementById('preview');
    let editandoId = null;
    const urlParams = new URLSearchParams(window.location.search);
    const idProducto = urlParams.get('id');

    // ==== Vista previa de imagen ====
    inputImagen.addEventListener('change', () => {
        const file = inputImagen.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => {
                previewImg.src = e.target.result;
                previewImg.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewImg.src = '';
            previewImg.style.display = 'none';
        }
    });

    // ==== CARGAR ARTISTAS Y CATEGORÍAS DINÁMICOS ====
    function cargarCatalogos() {
        return fetch('../func/listarCatalogos.php')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    llenarSelect('artista', data.artistas);
                    llenarSelect('categoria', data.categorias);
                } else {
                    Swal.fire('Error', 'No se pudo cargar artistas/categorías', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'Problema de conexión', 'error'));
    }

    function llenarSelect(idSelect, items) {
        const select = document.getElementById(idSelect);
        select.innerHTML = '<option value="">Seleccione una opción</option>';
        items.forEach(item => {
            select.innerHTML += `<option value="${item.id}">${item.nombre}</option>`;
        });
    }

    // ==== CREAR O EDITAR ====
    formCrear.addEventListener('submit', async e => {
        e.preventDefault();

        //imagen con Supabase
        const file = inputImagen.files[0];
        let imageUrl = '';

        // Si el usuario seleccionó una imagen, súbela al bucket
        if (file) {
            const fileName = `${Date.now()}-${file.name}`;
            const { data, error } = await supabase.storage
                .from('Imagen') // nombre del bucket
                .upload(fileName, file);

            if (error) {
                Swal.fire('Error', 'No se pudo subir la imagen: ' + error.message, 'error');
                return;
            }

            // Obtener URL pública
            const { data: publicUrlData } = supabase
                .storage
                .from('Imagen')
                .getPublicUrl(fileName);

            imageUrl = publicUrlData.publicUrl;
        }
        //fin imagen con Supabase

        const formData = new FormData(formCrear);
        formData.append('Accion', editandoId ? 'Editar' : 'Crear');
        if (editandoId) formData.append('id', editandoId);
        if (imageUrl) formData.append('imagen_url', imageUrl);

        fetch('../func/procesarproducto.php', { method: 'POST', body: formData })
            .then(async res => {
                // Verifica que la respuesta sea JSON y status 200
                const text = await res.text();
                try {
                    const data = JSON.parse(text);
                    return data;
                } catch (e) {
                    throw new Error('Respuesta no es JSON: ' + text);
                }
            })
            .then(data => {
                if (data.success) {
                    Swal.fire('✅ Éxito', data.message, 'success');
                    formCrear.reset();
                    previewImg.src = '';
                    previewImg.style.display = 'none';
                    editandoId = null;
                    cargarProductos();
                } else {
                    Swal.fire('❌ Error', data.message, 'error');
                }
            })
            .catch(err => {
                console.error('Error en fetch:', err);
                Swal.fire('❌ Error', 'Error de conexión con el servidor', 'error');
            });
    });

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
                    const artistaSelect = document.getElementById('artista');
                    const categoriaSelect = document.getElementById('categoria');
                    artistaSelect.value = p.artista_id || '';
                    categoriaSelect.value = p.categoria_id || '';
                    if (p.imagen_url) {
                        const previewImg = document.getElementById('preview');
                        previewImg.src = p.imagen_url;
                        previewImg.style.display = 'block';
                    }
                    editandoId = p.id;
                } else {
                    Swal.fire('Error', data.message || 'No se pudo cargar', 'error');
                }
            });
    };
    if (idProducto) {
        obtenerProducto(idProducto);
    }

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
    // Inicialización
    cargarCatalogos().then(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const idProducto = urlParams.get('id');
        if (idProducto) {
            obtenerProducto(idProducto);
        }
    });

    // ==== FILTRAR POR CATEGORÍA EN TIEMPO REAL ====
const botonesCategoria = document.querySelectorAll('.category-buttons button');
const gridProductos = document.getElementById('product-list-coleccion');
const inputBusqueda = document.getElementById('search-input');

botonesCategoria.forEach(btn => {
    btn.addEventListener('click', () => {
        // Quita la clase 'active' de todos y se la pone al clickeado
        botonesCategoria.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const categoria = btn.getAttribute('data-category');
        const termino = inputBusqueda.value.trim();

        // Llamada AJAX al PHP que devuelve productos filtrados en JSON
        fetch(`ajax/buscarProductos.php?q=${encodeURIComponent(termino)}&categoria=${encodeURIComponent(categoria)}`)
            .then(response => response.json())
            .then(data => {
                // Limpia el grid y agrega los productos
                gridProductos.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(prod => {
                        gridProductos.innerHTML += `
                            <div class="product-card">
                                <img src="${prod.imagen_url}" alt="${prod.nombre}">
                                <h3>${prod.nombre}</h3>
                                <p>${prod.descripcion}</p>
                                <p class="price">$${prod.precio}</p>
                            </div>
                        `;
                    });
                } else {
                    gridProductos.innerHTML = '<p style="text-align:center;">No se encontraron productos.</p>';
                }
            })
            .catch(err => {
                console.error('Error al filtrar productos:', err);
                gridProductos.innerHTML = '<p style="text-align:center;">Error al cargar productos.</p>';
            });
    });
});

// ==== FILTRAR POR TEXTO DEL BUSCADOR EN TIEMPO REAL ====
inputBusqueda.addEventListener('input', () => {
    const btnActivo = document.querySelector('.category-buttons button.active');
    const categoria = btnActivo ? btnActivo.getAttribute('data-category') : '';
    const termino = inputBusqueda.value.trim();

    fetch(`ajax/productosBusqueda.php?q=${encodeURIComponent(termino)}&categoria=${encodeURIComponent(categoria)}`)
        .then(response => response.json())
        .then(data => {
            gridProductos.innerHTML = '';
            if (data.length > 0) {
                data.forEach(prod => {
                    gridProductos.innerHTML += `
                        <div class="product-card">
                            <img src="${prod.imagen_url}" alt="${prod.nombre}">
                            <h3>${prod.nombre}</h3>
                            <p>${prod.descripcion}</p>
                            <p class="price">$${prod.precio}</p>
                        </div>
                    `;
                });
            } else {
                gridProductos.innerHTML = '<p style="text-align:center;">No se encontraron productos.</p>';
            }
        })
        .catch(err => {
            console.error('Error al filtrar productos:', err);
            gridProductos.innerHTML = '<p style="text-align:center;">Error al cargar productos.</p>';
        });
});

    // ==== INICIAL ====
    cargarProductos();
});