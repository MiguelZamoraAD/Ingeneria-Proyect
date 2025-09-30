document.addEventListener('DOMContentLoaded', () => {

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
    // Inicialización
    cargarCatalogos().then(() => {
        const urlParams = new URLSearchParams(window.location.search);
        const idProducto = urlParams.get('id');
        if (idProducto) {
            obtenerProducto(idProducto);
        }
    });

    // ==== INICIAL ====
});