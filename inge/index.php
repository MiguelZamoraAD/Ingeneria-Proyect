<!--
<?php 
session_start(); 
require_once __DIR__ . '/class/Usuarios.php';
require_once __DIR__ . '/class/productoCRUD.php';

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
//Archivo para mostrar productos
$productos = new ProductoCrud();
//Cantidad en el Carrito
$cartCount = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $cartCount += $item['cantidad'];
    }
}
?>
-->

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MelodyMart - Tienda de Instrumentos Musicales</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="resources/css/style.css">
    <link rel="stylesheet" href="resources/css/pag-botn.css">
    <link rel="stylesheet" href="resources/css/menu.css">
</head>

<body>
<header>
        <div class="container">
            <div class="logo">
                <a href="../index.php">Volumen Brutal 💿</a>
            </div>
            <nav calss="nav-menu">
                <button class="user-menu-toggle" arial-label="Abrir menú de navegación">&#9776; </button>
                <nav>
                    <ul class="nav-list">
                        <li><a href="pages/producto.php">Productos</a></li>
                        <?php if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === 'SI'): ?> 
                        <li class="user-profile">
                            <a href="#" id="profile-link">Mi Perfil</a>
                            <div class="profile-dropdown" id="profile-menu">
                                <a href="pages/perfil.php">Configuración</a>
                                <a href="#">Historial de Compras</a>
                                <a href="func/salir.php">Cerrar Sesión</a>
                            </div>
                        </li>
                        <?php endif; ?>
                        <?php if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== 'SI'): ?>
                        <li><a href="pages/session.php">Iniciar sección</a></li>
                        <?php endif; ?>
                        <li class="cart-icon">
                            <a href="pages/carrito.php" id="cart-link">🛒 Carrito (<span id="cart-count"><?= $cartCount ?></span>)</a>
                        </li>
                    </ul>
                </nav>
            </nav>
        </div>
    </header>

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="resources/js/supaBase.js"></script>
    <script src="resources/js/proCatalogo.js"></script>
    <script src="resources/js/categoria.js"></script>
    <script src="resources/js/logic.js"></script>
    <script src="resources/js/carrito.js"></script>
    
</body>

</html>

<?php include('resources/include/footer.php')?>