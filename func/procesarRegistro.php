<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // ocultamos errores visibles, se manejan en JSON

// Limpiar cualquier salida previa
while (ob_get_level()) {
    ob_end_clean();
}
ob_start();

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../class/Usuarios.php';
session_start();

try {
    $usuario = new UsuarioLogin();

    // Acción: verificar duplicado
    if (isset($_POST['Accion']) && $_POST['Accion'] === 'VerificarDuplicado') {
        $correo = $_POST['Correo'] ?? '';
        $existe = $usuario->existeCorreo($correo);
        ob_clean(); // limpiar buffer antes de enviar JSON
        echo json_encode(['existe' => $existe]);
        exit;
    }

    // Acción: guardar usuario
    if (isset($_POST['Accion']) && $_POST['Accion'] === 'Guardar') {
        $datos = [
            'nombreCompleto'  => $_POST['nombreCompleto'] ?? '',
            'correo'          => $_POST['correo'] ?? '',
            'password'        => $_POST['password'] ?? '',
            'confirmPassword' => $_POST['confirmPassword'] ?? '',
            'tipo'            => $_POST['tipo'] ?? 'user'
        ];

        $resultado = $usuario->registrar($datos);

        ob_clean(); // limpiar buffer antes de enviar JSON
        echo json_encode($resultado);
        exit;
    }

    // Acción: login
    if (isset($_POST['Accion']) && $_POST['Accion'] === 'Login') {
        $correo = $_POST['correo'] ?? '';
        $password = $_POST['password'] ?? '';

        $resultado = $usuario->login($correo, $password);

        ob_clean();
        echo json_encode($resultado);
        exit;
    }

    // Acción no válida
    ob_clean();
    echo json_encode(['error' => 'Acción no válida']);
    exit;

} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'error' => 'Error del servidor: ' . $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    exit;
}
