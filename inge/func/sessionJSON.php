<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === 'SI' && isset($_SESSION['usuario'])) {
    echo json_encode([
        'ok' => true,
        'correo' => $_SESSION['usuario'],
        'tipo' => $_SESSION['tipo'] ?? 'user'
    ]);
} else {
    echo json_encode([
        'ok' => false,
        'msg' => 'No hay sesión activa.'
    ]);
}