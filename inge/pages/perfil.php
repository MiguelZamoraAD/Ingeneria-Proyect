<!--<?php 
session_start(); 
require_once __DIR__ . '/../class/Usuarios.php';

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
    header('Location: session.php');
    exit();
}
//Archivo para mostrar el perfil del usuario
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
    <title>Mi Perfil - MelodyMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/style.css">
    <link rel="stylesheet" href="../resources/css/perfil.css">
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
                    <li><a href="pages/session.php">Iniciar sección</a></li>
                    <?php endif; ?>
                    <li class="cart-icon">
                        <a href="carrito.php" id="cart-link">🛒 Carrito (<span id="cart-count"><?= $cartCount ?></span>)</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    
    <main>
        <section class="profile-section">
            <div class="container profile-container">
                <div class="profile-header">
                    <h2>Información de Mi Perfil</h2>
                    <p>Gestiona tu información personal y de contacto.</p>
                </div>
                <div class="profile-info">
                    <div class="info-group">
                        <label for="name">Nombre Completo:</label>
                        <p id="name">Juan Pérez</p>
                    </div>
                    <div class="info-group">
                        <label for="email">Correo Electrónico:</label>
                        <p id="email">juan.perez@email.com</p>
                    </div>
                    <div class="info-group">
                        <label for="phone">Teléfono:</label>
                        <p id="phone">+52 55 1234 5678</p>
                    </div>
                    <div class="info-group">
                        <label for="address">Dirección de Envío:</label>
                        <p id="address">Calle Falsa 123, Ciudad de México, México</p>
                    </div>
                </div>
                <div class="profile-actions">
                    <button>Editar Información</button>
                </div>
            </div>
        </section>
    </main>

    <script src="../resources/js/perfil.js"></script>
    <script src="../resources/js/logic.js"></script>
    <script src="../resources/js/carrito.js"></script>
    <script src="../resources/js/script.js"></script>
    <script src="../resources/js/reporte.js"></script>
    <?php include('../resources/include/footer.php')?>
</body>

</html>