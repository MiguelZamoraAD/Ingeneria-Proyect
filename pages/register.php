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
//Archivo para registrar usuarios
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - MelodyMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/forms.css">
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
                    <li><a href="#categorias">Categorías</a></li>
                    <li><a href="#novedades">Novedades</a></li>
                    <li><a href="session.php">Iniciar sección</a></li>
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
                        <input type="hidden" name="tipo" value="user">

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