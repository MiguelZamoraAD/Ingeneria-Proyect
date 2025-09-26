<?php 
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
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - MelodyMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/style.css">
    <link rel="stylesheet" href="../resources/css/perfil.css">
</head>

<body>
    <header>
        <div class="container">
            <div class="logo">
                <a href="../index.php">MelodyMart 🎶</a>
            </div>
            <nav>
                <ul>
                    <li><a href="#productos">Productos</a></li>
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
                    <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'adm'): ?>
                    <li><a href="Producto.php">Agregar nuevos productos</a></li>
                    <?php endif; ?>
                    <?php if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== 'SI'): ?>
                    <li><a href="pages/session.php">Iniciar sección</a></li>
                    <?php endif; ?>
                    <li class="cart-icon">
                        <a href="#" id="cart-link">🛒 Carrito (<span id="cart-count">0</span>)</a>
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
</body>

</html>
<?php include('../resources/include/footer.php')?>