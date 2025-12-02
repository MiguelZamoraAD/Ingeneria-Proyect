<!--
<?php 
session_start(); 
require_once __DIR__ . '/../class/Usuarios.php';
require_once __DIR__ . '/../class/Conexion.php';
require_once __DIR__ . '/../class/productoCRUD.php';
require_once __DIR__ . '/../class/categoriaCRUD.php';

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
    //header('Location: session.php');
    //exit();
}
//Archivo para mostrar el perfil del usuario
//Cantidad en el Carrito
$cartCount = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $cartCount += $item['cantidad'];
    }
}
// Activar errores para depurar (puedes quitarlo luego)
ini_set('display_errors', 1);
error_reporting(E_ALL);
// Validar parámetro ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<h2>Error: No se proporcionó un ID de producto.</h2>";
    exit;
}
//categoria
$id = $_GET['id'];
$crud = new ProductoCrud();
$res  = $crud->obtener($id);
if (!$res['ok']) {
    echo "<h2>Error: {$res['msg']}</h2>";
    exit;
}
$producto = $res['producto'];
?>
-->

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../resources/css/style.css">
    <link rel="stylesheet" href="../resources/css/idProducto.css">
    <link rel="stylesheet" href="../resources/css/menu.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="../index.php">Volumen Brutal 💿</a>
            </div>
            <nav>
                <ul>
                    <li><a href="producto.php">Productos</a></li>
                    <?php if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === 'SI'): ?>
                    <li class="user-profile">
                        <a href="#" id="profile-link">Mi Perfil</a>
                        <div class="profile-dropdown" id="profile-menu">
                            <a href="perfil.php">Configuración</a>
                            <a href="#" id="btn-historial">Historial de Compras</a>
                            <a href="../func/salir.php">Cerrar Sesión</a>
                        </div>
                    </li>
                    <!-- Modal Generar Reporte -->
                    <div id="modal-historial" style="display:none;color:#333;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:1000;justify-content:center;align-items:center;">
                    </div>
                    <!-- Fin del Modal -->
                    <?php endif; ?>
                    <?php if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== 'SI'): ?>
                    <li><a href="session.php">Iniciar sección</a></li>
                    <?php endif; ?>
                    <li class="cart-icon">
                        <a href="carrito.php" id="cart-link">🛒 Carrito (<span id="cart-count"><?=$cartCount?></span>)</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
            <!-- -->
    <main class="product-page-content">
        <div class="product-wrapper">

            <div class="image-column">
                <div class="main-image-container">
                    <img src="<?= htmlspecialchars($producto['imagen_url'] ?? 'https://via.placeholder.com/400x400?text=Imagen+del+Producto') ?>" 
                         alt="<?= htmlspecialchars($producto['nombre'] ?? 'Producto') ?>" 
                         class="main-product-image">
                </div>
            </div>

            <div class="details-column">
                
                <div class="product-header-bar">
                    <h1><?= htmlspecialchars($producto['nombre']) ?></h1>
                </div>

                <div class="product-info-details">
                    <?php if (!empty($producto['artista_nombre'])): ?>
                        <p><strong>Artista:</strong> <?= htmlspecialchars($producto['artista_nombre']) ?></p>
                    <?php endif; ?>
                    <?php if (!empty($producto['categoria_nombre'])): ?>
                        <p><strong>Categoría:</strong> <?= htmlspecialchars($producto['categoria_nombre']) ?></p>
                    <?php endif; ?>
    
                    <p><strong>Descripción:</strong><br><?= nl2br(htmlspecialchars($producto['descripcion'] ?? 'Sin descripción')) ?></p>
                </div>
                
                <div class="cantity-availability">
                    <p><strong>Cantidad disponible:</strong> <?= htmlspecialchars($producto['cantidad']) ?><span class="available"></span></p>
                </div>
                
                <div class="price-section">
                    <p><strong>Precio:</strong> $<?= number_format($producto['precio'], 2) ?></p>
                    <!--
                    <span class="discount-tag"> 0% Que oferton</span>
                    <p class="offer-validity"></p>
                    -->
                </div>
                <?php if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === 'SI'): ?>
                <button class="add-to-cart-button">Agregar al carrito</button>
                <?php endif; ?>
                </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../resources/js/reporte.js"></script>
    <script src="../resources/js/carrito.js"></script>
    <script src="../resources/js/script.js"></script>
    <script src="../resources/js/logic.js"></script>
<?php include('../resources/include/footer.php')?>
</body>
</html>