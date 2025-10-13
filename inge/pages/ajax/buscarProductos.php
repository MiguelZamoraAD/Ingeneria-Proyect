<?php
require_once __DIR__ . '/../../class/productoCRUD.php';
$productos = new ProductoCrud();

//Obtener parámetros
$busqueda = $_GET['q'] ?? '';
$busqueda = trim($busqueda);
$categoria = $_GET['categoria'] ?? '';

//Listar productos con JOIN para obtener nombre de categoría
$res = $productos->listarConCategoria(1000, 1, $busqueda);
$result = $res['data'] ?? [];

// Filtrado por categoría
if (!empty($categoria) && strtolower($categoria) !== 'todos') {
    $result = array_filter($result, function($p) use($categoria) {
        return strtolower($p['categoria_nombre'] ?? '') === strtolower($categoria);
    });
}

// Devolver JSON
header('Content-Type: application/json');
echo json_encode(array_values($result));