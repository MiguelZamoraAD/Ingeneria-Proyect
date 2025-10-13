<!-- <?php
session_start();
require_once __DIR__ . '/../class/Usuarios.php';
$usuario = new UsuarioLogin();
require_once __DIR__ . '/../class/Conexion.php';
require_once __DIR__ . '/../class/productoCRUD.php';

// Comprobar si el usuario está autenticado
if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === 'SI') {
    // La lógica de la sesión se mantiene
    //var_dump($_SESSION['tipo'] ?? null);
} else {
    // Lógica para usuarios no logueados
}
//Cantidad en el Carrito
$cartCount = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $cartCount += $item['cantidad'];
    }
}
$totalCarrito = 0.0;
$productosCarrito = [];
if (isset($_SESSION['carrito']) && !empty($_SESSION['carrito'])) {
    $crud = new ProductoCrud();

    foreach ($_SESSION['carrito'] as $idProducto => $item) {
        // Obtener detalles del producto desde DB
        $res = $crud->obtener($idProducto);
        if ($res['ok']) {
            $producto = $res['producto'];
            $producto['cantidad'] = $item['cantidad'];
            $producto['subtotal'] = $producto['precio'] * $item['cantidad'];
            $productosCarrito[] = $producto;

            $cartCount += $item['cantidad'];
            $totalCarrito += $producto['subtotal'];
        }
   }
}
?>-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Rápida - Pago y Escaneo</title>

    <!-- Estilos -->
    <link rel="stylesheet" href="../resources/css/menu.css">
    <link rel="stylesheet" href="../resources/css/style.css">
    <link rel="stylesheet" href="../resources/css/carrito.css">

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
                        <a href="carrito.php" id="cart-link">🛒 Carrito (<span id="cart-count"><?= $cartCount ?></span>)</a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="contenedor-principal">

        <!-- SECCIÓN DEL CARRITO -->
        <section class="seccion-carrito lg:col-span-1">
            <h2>Mi Carrito</h2>
            <ul id="lista-carrito" class="max-h-80 overflow-y-auto mb-4">
                 <?php if (!empty($productosCarrito)): ?>
                    <?php foreach ($productosCarrito as $producto): ?>
                        <li class="item-carrito">
                            <img src="<?= htmlspecialchars($producto['imagen_url']) ?>"alt="<?= htmlspecialchars($producto['nombre']) ?>" class="miniatura">
                            <div class="detalles-item">
                                <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
                                <p>Cantidad: <?= $producto['cantidad'] ?></p>
                                <p>Precio: $<?= number_format($producto['precio'], 2) ?></p>
                                <p>Subtotal: <strong>$<?= number_format($producto['subtotal'], 2) ?></strong></p>
                                <form method="post" action="../func/eliminarDelCarrito.php" style="display:inline;">
                                    <input type="hidden" name="id" value="<?= $producto['id'] ?>">
                                    <button type="submit" class="btn-eliminar">❌ Eliminar</button>
                                </form>
                            </div>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li>Tu carrito está vacío 🛒</li>
                <?php endif; ?>
            </ul>
            <div class="resumen-carrito">
                <p class="text-xl font-bold">Total: <span id="total-carrito">$<?= number_format($totalCarrito, 2) ?></span></p>
                <button id="btn-proceder-pago" disabled>Proceder al Pago</button>
                <div id="mensaje-sistema" class="text-sm mt-3 p-2 bg-yellow-100 text-yellow-800 rounded hidden"></div>
            </div>
        </section>
        
        <!-- SECCIÓN DE PAGO (Inicialmente oculta) -->
        <section class="seccion-pago lg:col-span-3" style="display: none;"> 
            <div class="max-w-md mx-auto">
                <h2 class="text-center">Confirmación y Pago</h2>
                <p class="text-center text-2xl mb-6">Total a pagar: <strong id="total-pago-final" class="text-indigo-700">$0.00</strong></p>
                
                <form id="formulario-pago">
                    <p class="font-medium mb-3">Selecciona el Método de Pago:</p>
                    <div class="metodos-pago space-y-2">
                        <label>
                            <input type="radio" name="metodo-pago" value="tarjeta" required class="mr-2 text-indigo-600">
                            <span>💳 Tarjeta de Crédito/Débito</span>
                        </label>
                        <label>
                            <input type="radio" name="metodo-pago" value="efectivo" class="mr-2 text-indigo-600">
                            <span>💵 Efectivo</span>
                        </label>
                        <label>
                            <input type="radio" name="metodo-pago" value="transferencia" class="mr-2 text-indigo-600">
                            <span>🏦 Transferencia Bancaria</span>
                        </label>
                    </div>

                    <div id="detalles-tarjeta" class="mt-4 p-4 border rounded-lg bg-gray-50" style="display: none;">
                        <label for="numero-tarjeta" class="block font-medium mb-1">Número de Tarjeta (Simulado):</label>
                        <input type="text" id="numero-tarjeta" placeholder="XXXX XXXX XXXX XXXX" class="p-2 border rounded-lg w-full">
                    </div>

                    <button type="submit" id="btn-finalizar-compra" class="mt-6">
                        Finalizar Compra y Actualizar DB
                    </button>
                    <button type="button" id="btn-volver-carrito">
                        Volver al Carrito
                    </button>
                </form>
            </div>
        </section>

    </main>

    <!-- JS externo -->
    <script href="../resources/js/ArtCarrito.js"></script>
</body>
</html>
