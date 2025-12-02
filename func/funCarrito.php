<?php
session_start();
require_once __DIR__ . '/../class/Conexion.php';
require_once __DIR__ . '/../class/productoCRUD.php';

if (!isset($_POST['id'])) {
    echo json_encode(['ok' => false, 'msg' => 'ID no recibido']);
    exit;
}

$id = $_POST['id'];

$crud = new ProductoCrud();
$res = $crud->obtener($id);

if (!$res['ok']) {
    echo json_encode(['ok' => false, 'msg' => 'Producto no encontrado']);
    exit;
}

$productoDB = $res['producto'];
$stockReal = $productoDB['cantidad']; // stock real en DB

// Inicializa el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// Cantidad actual en el carrito
$cantidadActual = $_SESSION['carrito'][$id]['cantidad'] ?? 0;

// Validar stock antes de agregar
if ($cantidadActual + 1 > $stockReal) {
    echo json_encode([
        'ok' => false,
        'msg' => 'No hay suficiente stock disponible'
    ]);
    exit;
}

// Agregar o actualizar carrito
$_SESSION['carrito'][$id] = [
    'id' => $id,
    'cantidad' => $cantidadActual + 1
];

// Calcular total de items en el carrito
$totalItems = 0;
foreach ($_SESSION['carrito'] as $item) {
    $totalItems += $item['cantidad'];
}

// Calcular stock restante
$stockRestante = $stockReal - $_SESSION['carrito'][$id]['cantidad'];

echo json_encode([
    'ok' => true,
    'total' => $totalItems,
    'stockRestante' => $stockRestante
]);