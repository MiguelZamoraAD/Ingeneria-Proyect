<?php
require_once __DIR__ . '/../class/conexion.php';
header('Content-Type: application/json');

try {
    $conexion = new Conexion();
    $db = $conexion->getConexion();
    $artistas = $db->query("SELECT id, nombre FROM artista ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
    $categorias = $db->query("SELECT id, nombre FROM categoria ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['success' => true, 'artistas' => $artistas, 'categorias' => $categorias]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
