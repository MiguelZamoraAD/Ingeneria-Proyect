<!--
<?php 
session_start(); 
require_once __DIR__ . '/class/Usuarios.php';

$usuario = new UsuarioLogin();

// Comprobar si el usuario está autenticado
if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === 'SI') {
    //echo "Usuario autenticado ✅";

    // Opcional: también puedes mostrar el estado de la DB
    if ($usuario->estadoConexion()) {
        //echo " | Conexión a la DB activa ✅";
    } else {
        //echo " | Conexión a la DB fallida ❌";
    }

} else {
    //echo "No estás logeado ❌";
}
include('resources/include/header.php');
?>
-->
<?php include('resources/include/header.php') ?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MelodyMart - Tienda de Instrumentos Musicales</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/css/style.css">
</head>

<body>

    <main>
        <section class="hero-section">
            <div class="hero-content">
                <h1>Tu pasión, tu música, tu tienda.</h1>
                <p>Encuentra el instrumento perfecto para tu sonido único.</p>
                <div class="search-bar">
                    <input type="text" id="search-input" placeholder="Buscar guitarras, baterías, accesorios...">
                    <button id="search-button">Buscar</button>
                </div>
            </div>
        </section>

        <section id="categorias" class="category-section container">
            <h2>Explora por Categoría</h2>
            <div class="category-buttons">
                <button class="active" data-category="todos">Todos</button>
                <button data-category="guitarras">Guitarras</button>
                <button data-category="baterias">Baterías</button>
                <button data-category="teclados">Teclados</button>
                <button data-category="accesorios">Accesorios</button>
            </div>
        </section>

        <section id="productos" class="product-section container">
            <h2>Nuevos Productos</h2>
            <div class="product-grid" id="product-list-coleccion">
            
            </div>
        </section>
    </main>

    <div class="paginacion-usuarios">
        <button class="btnAnterior" id="btnAnterior">&laquo;</button>
        <span class="btn-paginacion activo" id="paginaActual"></span>
        <button class="btnSiguiente" id="btnSiguiente">&raquo;</button>
    </div>
    
    <script>
        window.usuarioTipo = "<?php echo $_SESSION['tipo'] ?? ''; ?>"; // 'adm' para admin
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script src="resources/js/supaBase.js"></script>
    <script src="resources/js/proCatalogo.js"></script>
</body>

</html>

<?php include('resources/include/footer.php')?>