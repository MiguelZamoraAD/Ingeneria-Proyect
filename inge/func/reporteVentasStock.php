<?php
/*
session_start();
require_once __DIR__ . '/../class/Conexion.php';
require_once __DIR__ . '/../class/productoCRUD.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'adm') {
    http_response_code(403);
    echo json_encode(["error" => "Acceso denegado"]);
    exit();
}

$conexion = (new Conexion())->getConexion();

// Parámetros recibidos
$categoria = $_GET['categoria'] ?? 'todas';
$formato = $_GET['formato'] ?? 'pdf';

// ---------------------- //
// 🔹 CONSULTA DE VENTAS 🔹 //
// ---------------------- //
$whereCategoria = ($categoria !== 'todas') ? "WHERE p.categoria = :categoria" : "";

$sqlVentas = "
    SELECT 
        pd.producto_id, 
        p.nombre, 
        SUM(pd.cantidad) AS cantidad_vendida,
        SUM(pd.subtotal) AS subtotal_total,
        SUM(pe.itbms) AS itbms_total,
        SUM(pe.total) AS total_general
    FROM pedidodetalle pd
    JOIN pedidos pe ON pd.pedido_id = pe.id
    JOIN producto p ON pd.producto_id = p.id
    $whereCategoria
    GROUP BY pd.producto_id, p.nombre
    ORDER BY p.nombre;
";

$stmt = $conexion->prepare($sqlVentas);
if ($categoria !== 'todas') $stmt->bindParam(':categoria', $categoria);
$stmt->execute();
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular totales
$totalVentas = 0;
$totalItbms = 0;
foreach ($ventas as $v) {
    $totalVentas += $v['total_general'];
    $totalItbms += $v['itbms_total'];
}

// --------------------- //
// 🔹 CONSULTA DE STOCK 🔹 //
// --------------------- //
$sqlStock = "
    SELECT 
        id, 
        nombre, 
        cantidad AS cantidad_disponible, 
        precio AS precio_unitario, 
        (cantidad * precio) AS valor_total
    FROM producto
    " . ($categoria !== 'todas' ? "WHERE categoria = :categoria" : "") . "
    ORDER BY nombre;
";

$stmtStock = $conexion->prepare($sqlStock);
if ($categoria !== 'todas') $stmtStock->bindParam(':categoria', $categoria);
$stmtStock->execute();
$stock = $stmtStock->fetchAll(PDO::FETCH_ASSOC);

// --------------------------- //
// 🔹 EXPORTAR A EXCEL OPCIONAL 🔹 //
// --------------------------- //
if ($formato === 'excel') {
    require_once __DIR__ . '/../vendor/autoload.php';
    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Ventas y Stock');

    // Sección Ventas
    $sheet->setCellValue('A1', "Reporte de Ventas - Categoría: " . ucfirst($categoria));
    $sheet->mergeCells('A1:E1');
    $sheet->fromArray(['Producto', 'Cantidad Vendida', 'Subtotal', 'ITBMS', 'Total'], NULL, 'A3');

    $row = 4;
    foreach ($ventas as $v) {
        $sheet->fromArray([
            $v['nombre'],
            $v['cantidad_vendida'],
            number_format($v['subtotal_total'], 2),
            number_format($v['itbms_total'], 2),
            number_format($v['total_general'], 2)
        ], NULL, "A{$row}");
        $row++;
    }

    $row += 2;
    $sheet->setCellValue("A{$row}", "Inventario / Stock - Categoría: " . ucfirst($categoria));
    $sheet->mergeCells("A{$row}:D{$row}");
    $row += 2;
    $sheet->fromArray(['Producto', 'Stock', 'Precio Unitario', 'Valor Total'], NULL, "A{$row}");
    $row++;

    foreach ($stock as $s) {
        $sheet->fromArray([
            $s['nombre'],
            $s['cantidad_disponible'],
            number_format($s['precio_unitario'], 2),
            number_format($s['valor_total'], 2)
        ], NULL, "A{$row}");
        $row++;
    }

    // Totales
    $row += 2;
    $sheet->setCellValue("A{$row}", "Totales Generales");
    $sheet->setCellValue("B{$row}", "Ventas: " . number_format($totalVentas, 2));
    $sheet->setCellValue("C{$row}", "ITBMS: " . number_format($totalItbms, 2));
    $sheet->setCellValue("D{$row}", "Total: " . number_format($totalVentas + $totalItbms, 2));

    // Descargar
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="Reporte_' . $categoria . '_' . date('Ymd') . '.xlsx"');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}

// -------------------- //
// 🔹 SALIDA EN JSON 🔹 //
// -------------------- //
header('Content-Type: application/json');
echo json_encode([
    "ventas" => $ventas,
    "stock" => $stock,
    "totales" => [
        "total_ventas" => $totalVentas,
        "total_itbms" => $totalItbms,
        "total_general" => $totalVentas + $totalItbms
    ]
]);

*///
session_start();
require_once __DIR__ . '/../class/Conexion.php';
require_once __DIR__ . '/../class/productoCRUD.php';

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'adm') {
    http_response_code(403);
    echo json_encode(["error" => "Acceso denegado"]);
    exit();
}

$conexion = (new Conexion())->getConexion();

// ---------------------- //
// 🔹 CONSULTA DE VENTAS 🔹 //
// ---------------------- //
$ventas = [];
$totalVentas = 0;
$totalItbms = 0;

$query = $conexion->query("
    SELECT 
        pd.producto_id, 
        p.nombre, 
        SUM(pd.cantidad) AS cantidad_vendida,
        SUM(pd.subtotal) AS subtotal_total,
        SUM(pe.itbms) AS itbms_total,
        SUM(pe.total) AS total_general
    FROM pedidodetalle pd
    JOIN pedidos pe ON pd.pedido_id = pe.id
    JOIN producto p ON pd.producto_id = p.id
    GROUP BY pd.producto_id, p.nombre
    ORDER BY p.nombre;
");

foreach ($query->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $ventas[] = $row;
    $totalVentas += $row['total_general'];
    $totalItbms += $row['itbms_total'];
}

// --------------------- //
// 🔹 CONSULTA DE STOCK 🔹 //
// --------------------- //
$stock = [];
$queryStock = $conexion->query("
    SELECT 
        id, 
        nombre, 
        cantidad AS cantidad_disponible, 
        precio AS precio_unitario, 
        (cantidad * precio) AS valor_total
    FROM producto 
    ORDER BY nombre;
");

foreach ($queryStock->fetchAll(PDO::FETCH_ASSOC) as $row) {
    $stock[] = $row;
}

// -------------------- //
// 🔹 SALIDA EN JSON 🔹 //
// -------------------- //
header('Content-Type: application/json');
echo json_encode([
    "ventas" => $ventas,
    "stock" => $stock,
    "totales" => [
        "total_ventas" => $totalVentas,
        "total_itbms" => $totalItbms,
        "total_general" => $totalVentas + $totalItbms
    ]
]);
//