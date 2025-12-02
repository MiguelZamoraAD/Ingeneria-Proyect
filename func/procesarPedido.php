<?php
session_start();
require_once __DIR__ . '/../class/Conexion.php';
require_once __DIR__ . '/../class/productoCRUD.php';

if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== 'SI') {
    header('Location: ../pages/session.php');
    exit();
}

if (empty($_SESSION['carrito'])) {
    echo "Carrito vacío";
    exit();
}

$correo = $_SESSION['usuario']; // Asegúrate de guardar el correo en la sesión al iniciar
$ITBMS = 0.07; // 7%

$conexion = (new Conexion())->getConexion();
$crud = new ProductoCrud();

try {
    $conexion->beginTransaction();

    // Calcular subtotal
    $subtotal = 0;
    foreach ($_SESSION['carrito'] as $id => $item) {
        $res = $crud->obtener($id);
        if ($res['ok']) {
            $producto = $res['producto'];
            $subtotal += $producto['precio'] * $item['cantidad'];
        }
    }

    $itbms = $subtotal * $ITBMS;
    $total = $subtotal + $itbms;

    // Insertar pedido en tabla pedidos
    $stmt = $conexion->prepare(
        "INSERT INTO Pedidos (correo_usuario, subtotal, itbms, total) VALUES (?, ?, ?, ?) RETURNING id"
    );
    $stmt->execute([$correo, $subtotal, $itbms, $total]);
    $pedidoId = $stmt->fetchColumn();

    // Insertar productos del pedido en pedidodetalle y actualizar stock
    foreach ($_SESSION['carrito'] as $id => $item) {
        $res = $crud->obtener($id);
        if ($res['ok']) {
            $producto = $res['producto'];
            $subtotalProducto = $producto['precio'] * $item['cantidad'];

            // Insertar detalle
            $stmt = $conexion->prepare(
                "INSERT INTO PedidoDetalle (pedido_id, producto_id, nombre, cantidad, subtotal) VALUES (?, ?, ?, ?, ?)"
            );
            $stmt->execute([$pedidoId, $id, $producto['nombre'], $item['cantidad'], $subtotalProducto]);

            // Actualizar stock
            $crud->actualizarCantidad($id, $item['cantidad']);
        }
    }

    $conexion->commit();

    // Vaciar carrito
    unset($_SESSION['carrito']);

    // Redirigir con éxito
    header('Location: ../pages/carrito.php?exito=1');
    exit();

} catch (Exception $e) {
    $conexion->rollBack();
    echo "Error al procesar el pedido: " . $e->getMessage();
}
?>
