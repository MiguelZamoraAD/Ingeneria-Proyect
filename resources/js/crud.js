document.addEventListener('DOMContentLoaded', () => {
    const formCrear = document.getElementById('productoForm');
    const inputImagen = document.getElementById('imagen');
    const urlParams = new URLSearchParams(window.location.search);
    const idProducto = urlParams.get('id');
    const previewImg = document.getElementById('preview');
    let editandoId = null;

    // ==== CREAR ====
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
        } else if (editandoId) {
            //Se mantiene la imagen Actual si se edita
            imageUrl = document.getElementById('preview').src || '';
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
                } else {
                    Swal.fire('❌ Error', data.message, 'error');
                }
            })
            .catch(err => {
                console.error('Error en fetch:', err);
                Swal.fire('❌ Error', 'Error de conexión con el servidor', 'error');
            });
    });

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
                    //se guarda el ID
                    editandoId = p.id;
                    //Cambiar texto para que se vea bonito
                    document.querySelector('.registro-header h2').textContent = 'Actualizar Producto';
                    document.querySelector('.btnRegistrar').textContent = 'Actualizar Producto';

                    // Mostrar imagen previa si existe
                    if (p.imagen_url) {
                        const previewImg = document.getElementById('preview');
                        previewImg.src = p.imagen_url;
                        previewImg.style.display = 'block';
                    }
                } else {
                    Swal.fire('Error', data.message || 'No se pudo cargar', 'error');
                }
            });
    };
    if (idProducto) {
        obtenerProducto(idProducto);
    }

});