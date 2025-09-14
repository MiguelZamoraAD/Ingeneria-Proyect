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
//Archivo para registrar nuevos productos
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Producto - MelodyMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/product.css">
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
                            <a href="func/salir.php">Cerrar Sesión</a>
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
        <section class="registro-section">
            <div class="container registro-container">
                <div class="registro-header">
                    <h2>Registra un Nuevo Producto</h2>
                    <p>Completa los campos para añadir un nuevo artículo a tu inventario.</p>
                </div>
                <div class="registro-form-container">
                    <form id="productoForm" action="procesarProducto.php" method="POST" class="registro-form">
                        
                        <div class="form-group">
                            <label for="nombre">Nombre del Producto</label>
                            <input type="text" id="nombre" name="nombre" required>
                        </div>

                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
                        </div>
                        
                        <div class="form-group">
                            <label for="precio">Precio</label>
                            <input type="number" id="precio" name="precio" step="0.01" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="sku">SKU (Código Interno)</label>
                            <input type="text" id="sku" name="sku" required>
                        </div>

                        <div class="form-group">
                            <label for="codigo_barra">Código de Barras</label>
                            <input type="text" id="codigo_barra" name="codigo_barra">
                        </div>

                        <div class="form-group">
                            <label for="cantidad">Cantidad en Stock</label>
                            <input type="number" id="cantidad" name="cantidad" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="categoria">Categoría</label>
                            <input type="number" id="categoria" name="categoria_id" min="1" required>
                        </div>

                        <div class="form-group">
                            <label for="marca">Marca</label>
                            <input type="number" id="marca" name="marca_id" min="1" required>
                        </div>

                        <div class="form-group">
                            <label for="stock_minimo">Stock Mínimo</label>
                            <input type="number" id="stock_minimo" name="stock_minimo" min="0">
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btnRegistrar">Registrar Producto</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../resources/js/producto.js"></script>
    <script src="../resources/js/perfil.js"></script>
</body>

</html>
<?php include('../resources/include/footer.php')?>