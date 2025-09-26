<?php
// func/CRUD/productoFunc.php

// ===== CONFIGURACIÓN =====
ini_set('display_errors', 0); // No mostrar errores en pantalla
error_reporting(E_ALL);

// Forzar JSON
header('Content-Type: application/json');

// ===== INCLUYENDO CLASE =====
require_once __DIR__ . '/../class/productoCRUD.php';

// ===== RESPUESTA INICIAL =====
$response = ['success'=>false,'message'=>'','accion'=>'','data'=>[]];

try {
    $accion = $_POST['Accion'] ?? '';
    $crud   = new ProductoCrud();

    // ==== VALIDAR CAMPOS REQUERIDOS PARA CREAR/EDITAR ====
    if (in_array($accion, ['Crear','Editar'])) {
        $required = ['nombre','descripcion','precio','cantidad','artista_id','categoria_id'];
        $missing  = [];
        foreach ($required as $field) {
            if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
                $missing[] = $field;
            }
        }
        if (!empty($missing)) {
            $response['message'] = 'Faltan datos requeridos: ' . implode(', ', $missing);
            echo json_encode($response);
            exit;
        }
    }

    // ==== ACCIONES CRUD ====
    switch ($accion) {
        case 'Crear':
            $res = $crud->crear($_POST);
            $response['success'] = $res['ok'];
            $response['message'] = $res['msg'];
            $response['accion']  = 'Crear';
            break;

        case 'Editar':
            $id = $_POST['id'] ?? '';
            $res = $crud->editar($id, $_POST);
            $response['success'] = $res['ok'];
            $response['message'] = $res['msg'];
            $response['accion']  = 'Editar';
            break;

        case 'Obtener':
            $id = $_POST['id'] ?? '';
            $res = $crud->obtener($id);
            $response['success'] = $res['ok'];
            if ($res['ok']) $response['producto'] = $res['producto'];
            $response['message'] = $res['msg'];
            $response['accion']  = 'Obtener';
            break;

        case 'Eliminar':
            $id = $_POST['id'] ?? '';
            $res = $crud->eliminar($id);
            $response['success'] = $res['ok'];
            $response['message'] = $res['msg'];
            $response['accion']  = 'Eliminar';
            break;
        
        //Para listar los productos con paginación y búsqueda
        /*case 'Listar': 
            $pagina  = max(1, intval($_POST['pagina'] ?? 1));
            $limite  = intval($_POST['limite'] ?? 10);
            $busqueda= trim($_POST['busqueda'] ?? '');
            $res     = $crud->listar($limite, $pagina, $busqueda);
            $response = array_merge($response, $res);
            $response['success'] = true;
            $response['accion']  = 'Listar';
            break;
            */

        default:
            $response['message'] = 'Acción no válida';
    }

} catch (Throwable $e) {
    // Captura cualquier error inesperado
    $response['message'] = 'Error interno: '.$e->getMessage();
}

// ==== DEVOLVER JSON LIMPIO ====
ob_clean();
echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
