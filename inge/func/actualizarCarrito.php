<?php
session_start();

if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== 'SI') {
    echo json_encode(['ok'=>false,'msg'=>'No autenticado']);
    exit();
}

$id = $_POST['id'] ?? null;
$accion = $_POST['accion'] ?? null;

if (!$id || !in_array($accion, ['sumar','restar'])) {
    echo json_encode(['ok'=>false,'msg'=>'Datos incorrectos']);
    exit();
}

if (!isset($_SESSION['carrito'][$id])) {
    echo json_encode(['ok'=>false,'msg'=>'Producto no en carrito']);
    exit();
}

if ($accion === 'sumar') {
    $_SESSION['carrito'][$id]['cantidad']++;
} elseif ($accion === 'restar') {
    $_SESSION['carrito'][$id]['cantidad']--;
    if ($_SESSION['carrito'][$id]['cantidad'] <= 0) {
        unset($_SESSION['carrito'][$id]);
    }
}

echo json_encode(['ok'=>true,'cantidad'=>$_SESSION['carrito'][$id]['cantidad'] ?? 0]);
