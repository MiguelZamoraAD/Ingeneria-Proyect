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
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - MelodyMart</title>
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
                    <?php if (isset($_SESSION['autenticado']) === 'SI'): ?>
                    <li class="user-profile">
                        <a href="#" id="profile-link">Mi Perfil</a>
                        <div class="profile-dropdown" id="profile-menu">
                            <a href="pages/perfil.php">Configuración</a>
                            <a href="#">Historial de Compras</a>
                            <a href="func/salir.php">Cerrar Sesión</a>
                        </div>
                    </li>
                    <?php endif; ?>
                    <li class="cart-icon">
                        <a href="#" id="cart-link">🛒 Carrito (<span id="cart-count">0</span>)</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="registro-section">
            <div class="container registro-container">
                <div class="registro-header">
                    <h2>Crea tu Cuenta en MelodyMart</h2>
                    <p>Únete a nuestra comunidad y descubre la mejor música.</p>
                </div>
                <div class="registro-form-container">

                    <form id="registroForm" action="procesarRegistro.php" method="POST" class="registro-form">

                        <div class="form-group">
                            <label for="nombre">Nombre Completo</label>
                            <input type="text" id="nombre" name="nombreCompleto" required>
                        </div>

                        <div class="form-group">
                            <label for="correo">Correo Electrónico</label>
                            <input type="email" id="correo" name="correo" autocomplete="username" required >
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" id="password" name="password" autocomplete="new-password" required>
                        </div>

                        <div class="form-group">
                            <label for="confirmPassword">Confirmar Contraseña</label>
                            <input type="password" id="confirmPassword" name="confirmPassword" autocomplete="new-password" required>
                        </div>

                        <!-- Selección de tipo de usuario -->
                        <div class="form-group">
                            <label for="tipo">Tipo de Usuario</label>
                            <select id="tipo" name="tipo" required>
                                <option value="">Seleccione un tipo</option>
                                <option value="user">Usuario</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>

                        <div class="form-actions">
                            <button type="button" id="btnRegistrar" class="btnRegistrar">Registrarse</button>
                        </div>
                    </form>
                </div>
                <p class="login-link">¿Ya tienes una cuenta? <a href="session.php">Inicia Sesión aquí</a></p>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../resources/js/user.js"></script>
</body>

</html>
<?php include('../resources/include/footer.php')?>