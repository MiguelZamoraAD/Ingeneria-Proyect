<!-- <?php
session_start();
require_once __DIR__ . '/../class/Usuarios.php';
require_once __DIR__ . '/../class/productoCRUD.php';
require_once __DIR__ . '/../class/categoriaCRUD.php';
$usuario = new UsuarioLogin();

// Comprobar si el usuario está autenticado
if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === 'SI') {
    // La lógica de la sesión se mantiene
    //var_dump($_SESSION['tipo'] ?? null);
} else {
    // Lógica para usuarios no logueados
}
//Archivo para mostrar productos
$productos = new ProductoCrud();
$categoriaCrud = new CategoriaCRUD();
$categorias = $categoriaCrud->listar();
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
    <title>MelodyMart - Colección y Merchandising</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/style.css">
    <link rel="stylesheet" href="../resources/css/pag-botn.css">
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
                    <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'adm'): ?>
                    <li><a href="registroProducto.php">Agregar nuevos productos</a></li>
                    <?php endif; ?>
                    <!-- <?php if (isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'adm'): ?>-->
                    <li><a href="#" id="btn-generar-reporte">Generar Reporte</a></li>
                    <!-- <?php endif; ?> -->
                    
                    <!-- Modal Generar Reporte -->
                    <div id="modal-reporte" style="display:none;color:#333;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.6);z-index:1000;justify-content:center;align-items:center;">
                        <div style="background:#fff; padding:20px; border-radius:8px; width:420px; text-align:center; position:relative;">
                            <h3>Generar Reporte de Ventas y Stock</h3>

                            <label for="categoria">Categoría:</label><br>
                            <select id="categoria" style="margin:10px; padding:6px; width:90%;">
                                <option value="todas">Todas</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo htmlspecialchars($cat['id']); ?>">
                                        <?php echo htmlspecialchars($cat['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select><br>

                            <label for="formato">Formato:</label><br>
                            <select id="formato" style="margin:10px; padding:6px; width:90%;">
                                <option value="pdf">PDF</option>
                                <option value="xlsx">Excel</option>
                            </select><br>

                            <div style="display:flex; justify-content:center; gap:8px; margin-top:10px;">
                                <button id="confirmar-reporte" style="padding:10px 20px;">✅ Generar</button>
                                <button id="cancelar-reporte" style="padding:10px 20px;">❌ Cancelar</button>
                            </div>
                        </div>
                    </div>
                    <!-- Fin del Modal -->
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
                        <a href="carrito.php" id="cart-link">🛒 Carrito (<span id="cart-count"><?= $cartCount ?></span>)</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <section class="hero-section hero-coleccion">
            <div class="hero-content">
                <h1>Descubre nuestros productos</h1>
                <div class="search-bar">
                    <input type="text" id="search-input" placeholder="Buscar productos...">
                </div>
                <!--/////categorías/////-->
                <section class="category-section">
                    <div class="category-buttons">
                        <button data-category="todos">Todos</button>
                        <?php foreach($categorias as $cat): ?>
                            <button data-category="<?php echo htmlspecialchars($cat['nombre']); ?>">
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </section>
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
    <script src="../resources/js/categoria.js"></script>
    <script src="../resources/js/logic.js"></script>
    <script src="../resources/js/carrito.js"></script>
    <!-- Descarga en Pdf y Reporte-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="../resources/js/reporte.js"></script>
    <script src="../resources/js/script.js"></script>
<?php include('../resources/include/footer.php')?>
</body>

</html>