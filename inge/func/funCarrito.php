<?php
session_start();
<<<<<<< Updated upstream:inge/func/funCarrito.php
<<<<<<< Updated upstream:inge/func/funCarrito.php
=======
require_once __DIR__ . '/../class/Conexion.php';
require_once __DIR__ . '/../class/productoCRUD.php';
>>>>>>> Stashed changes:func/funCarrito.php
=======
require_once __DIR__ . '/../class/Conexion.php';
require_once __DIR__ . '/../class/productoCRUD.php';
>>>>>>> Stashed changes:func/funCarrito.php

if (!isset($_POST['id'])) {
    echo json_encode(['ok' => false, 'msg' => 'ID no recibido']);
    exit;
}

$id = $_POST['id'];

<<<<<<< Updated upstream:inge/func/funCarrito.php
<<<<<<< Updated upstream:inge/func/funCarrito.php
=======
=======
>>>>>>> Stashed changes:func/funCarrito.php
$crud = new ProductoCrud();
$res = $crud->obtener($id);

if (!$res['ok']) {
    echo json_encode(['ok' => false, 'msg' => 'Producto no encontrado']);
    exit;
}

$productoDB = $res['producto'];
$stockReal = $productoDB['cantidad']; // stock real en DB

<<<<<<< Updated upstream:inge/func/funCarrito.php
>>>>>>> Stashed changes:func/funCarrito.php
=======
>>>>>>> Stashed changes:func/funCarrito.php
// Inicializa el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

<<<<<<< Updated upstream:inge/func/funCarrito.php
<<<<<<< Updated upstream:inge/func/funCarrito.php
// Si el producto ya está en el carrito, incrementa la cantidad
if (isset($_SESSION['carrito'][$id])) {
    $_SESSION['carrito'][$id]['cantidad']++;
} else {
    // Aquí podrías traer datos del producto si quieres guardarlos también
    $_SESSION['carrito'][$id] = [
        'id' => $id,
        'cantidad' => 1
    ];
}

// Calcular la cantidad total de productos
$total = 0;
foreach ($_SESSION['carrito'] as $item) {
    $total += $item['cantidad'];
}

echo json_encode(['ok' => true, 'total' => $total]);
=======
=======
>>>>>>> Stashed changes:func/funCarrito.php
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
<<<<<<< Updated upstream:inge/func/funCarrito.php
]);
>>>>>>> Stashed changes:func/funCarrito.php
=======
]);
>>>>>>> Stashed changes:func/funCarrito.php
