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
                    <h2>Inicia sesion en MelodyMart</h2>
                    <p>Únete a nuestra comunidad y descubre la mejor música.</p>
                </div>
                <div class="registro-form-container">
                    <form id="loginForm" class="registro-form">
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" id="loginEmail" name="correo" autocomplete="password" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" id="loginPassword" name="password" autocomplete="new-password" required>
                        </div>
                        <div class="form-actions">
                            <button type="button" id="btnLogin">Iniciar sesión</button>
                        </div>
                    </form>
                </div>
                <p>¿Aun no tienes una cuenta? <a href="register.php">Registrate aquí</a></p>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../resources/js/user.js"></script>
</body>

</html>
<?php include('../resources/include/footer.php')?>