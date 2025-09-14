<?php
require_once 'class/Conexion.php';

$db = new Conexion();
$conn = $db->getConexion();

// Consulta de prueba (cambiar a una tabla real que hayas creado)
$stmt = $conn->query("SELECT NOW()");
$row = $stmt->fetch();
echo "Hora actual desde Supabase: " . $row['now'];
?>
