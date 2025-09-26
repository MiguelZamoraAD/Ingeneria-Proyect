const supabaseUrl = 'https://recghdynvcvyzdrtmouj.supabase.co';
const supabaseKey = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6InJlY2doZHludmN2eXpkcnRtb3VqIiwicm9sZSI6ImFub24iLCJpYXQiOjE3NTc1NTU4MzcsImV4cCI6MjA3MzEzMTgzN30.l7O6l_P3k0TinXjRbj9v6EN0x6iXzLxcuQEUqVtyfdE'; // Usa la clave anónima pública
const supabase = window.supabase.createClient(supabaseUrl, supabaseKey);

document.addEventListener('DOMContentLoaded', () => {
    const formCrear = document.getElementById('productoForm');
    const inputImagen = document.getElementById('imagen');
    const previewImg = document.getElementById('preview');
    let editandoId = null;

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
        fetch('../func/listarCatalogos.php')
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
                    document.getElementById('artista').value = p.artista_id || '';
                    document.getElementById('categoria').value = p.categoria_id || '';
                    editandoId = p.id;
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

    // ==== INICIAL ====
    cargarCatalogos();
    cargarProductos();
});