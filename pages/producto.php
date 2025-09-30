<!-- <?php
session_start();
require_once __DIR__ . '/../class/Usuarios.php';
$usuario = new UsuarioLogin();

// Comprobar si el usuario está autenticado
if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === 'SI') {
    // La lógica de la sesión se mantiene
    //var_dump($_SESSION['tipo'] ?? null);
} else {
    // Lógica para usuarios no logueados
}
//Archivo para mostrar productos
 
?>
-->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MelodyMart - Colección y Merchandising</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/style.css">
    <link rel="stylesheet" href="../resources/css/pag-botn.css">
</head>

<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="../index.php">MelodyMart 🎶</a>
            </div>
            <nav>
                <ul>
                    <li><a href="producto.php">Productos</a></li>
                    <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'adm'): ?>
                    <li><a href="registroProducto.php">Agregar nuevos productos</a></li>
                    <?php endif; ?>
                    <li><a href="#categorias">Categorías</a></li>
                    <li><a href="#novedades">Novedades</a></li>
                    <?php if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === 'SI'): ?>
                    <li class="user-profile">
                        <a href="#" id="profile-link">Mi Perfil</a>
                        <div class="profile-dropdown" id="profile-menu">
                            <a href="perfil.php">Configuración</a>
                            <a href="#">Historial de Compras</a>
                            <a href="../func/salir.php">Cerrar Sesión</a>
                        </div>
                    </li>
                    <?php endif; ?>
                    <?php if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== 'SI'): ?>
                    <li><a href="session.php">Iniciar sección</a></li>
                    <?php endif; ?>
                    <li class="cart-icon">
                        <a href="carrito.php" id="cart-link">🛒 Carrito (<span id="cart-count">0</span>)</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero-section hero-coleccion">
            <div class="hero-content">
                <h1>Descubre tesoros musicales.</h1>
                <p>Instrumentos de colección, vinilos exclusivos y merchandising de tus artistas favoritos.</p>
                <div class="search-bar">
                    <input type="text" id="search-input" placeholder="Buscar vinilos, posters, memorabilia...">
                    <button id="search-button">Buscar</button>
                </div>
            </div>
        </section>

        <section id="productos-coleccion" class="product-section container">
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
        window.usuarioTipo = "<?php echo $_SESSION['tipo'] ?? ''; ?>";
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../resources/js/supaBase.js"></script>
    <script src="../resources/js/proCatalogo.js"></script>
    <script src="../resources/js/pagina.js"></script>
</body>

</html>

<?php include('../resources/include/footer.php')?>