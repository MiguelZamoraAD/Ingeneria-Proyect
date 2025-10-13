<?php
session_start();

if (!isset($_POST['id'])) {
    echo json_encode(['ok' => false, 'msg' => 'ID no recibido']);
    exit;
}

$id = $_POST['id'];

// Inicializa el carrito si no existe
if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

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
