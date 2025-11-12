<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// reporteVentasStock.php
session_start();
require_once __DIR__ . '/../class/Conexion.php';
require_once __DIR__ . '/../vendor/autoload.php'; // ajusta ruta según donde esté vendor

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\Axis;

// seguridad: solo admin
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'adm') {
    http_response_code(403);
    echo json_encode(["error" => "Acceso denegado"]);
    exit();
}

$conexion = (new Conexion())->getConexion();

// parámetros
$formato = $_GET['formato'] ?? 'json'; // json (para pdf) o xlsx
$categoria = $_GET['categoria'] ?? 'todas';
$isAll = ($categoria === 'todas');

// ---------- Consultas ---------- //
// 1) Ventas agrupadas por producto (filtrado por categoría si aplica)
$sqlVentas = "
    SELECT pd.producto_id, p.nombre,
           SUM(pd.cantidad) AS cantidad_vendida,
           SUM(pd.subtotal) AS subtotal_total,
           SUM(pe.itbms) AS itbms_total,
           SUM(pe.total) AS total_general
    FROM pedidodetalle pd
    JOIN pedidos pe ON pd.pedido_id = pe.id
    JOIN producto p ON pd.producto_id = p.id
";
$params = [];
if (!$isAll) {
    $sqlVentas .= " WHERE p.categoria_id = :categoria ";
    $params[':categoria'] = $categoria;
}
$sqlVentas .= " GROUP BY pd.producto_id, p.nombre ORDER BY p.nombre; ";

$stmt = $conexion->prepare($sqlVentas);
$stmt->execute($params);
$ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2) Stock (productos) — filtrado por categoría si aplica
$sqlStock = "
    SELECT id, nombre, cantidad AS cantidad_disponible, precio AS precio_unitario, (cantidad * precio) AS valor_total, categoria_id
    FROM producto
";
$params = [];
if (!$isAll) {
    $sqlStock .= " WHERE categoria_id = :categoria ";
    $params[':categoria'] = $categoria;
}
$sqlStock .= " ORDER BY nombre;";

$stmt2 = $conexion->prepare($sqlStock);
$stmt2->execute($params);
$stock = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// 3) Datos para gráficas
// 3a) Producto más vendido (por cantidad)
$sqlTop = "
    SELECT pd.producto_id, p.nombre, SUM(pd.cantidad) AS cantidad_vendida
    FROM pedidodetalle pd
    JOIN producto p ON pd.producto_id = p.id
";
$params = [];
if (!$isAll) {
    $sqlTop .= " WHERE p.categoria_id = :categoria ";
    $params[':categoria'] = $categoria;
}
$sqlTop .= " GROUP BY pd.producto_id, p.nombre ORDER BY cantidad_vendida DESC LIMIT 10;";
$stmt3 = $conexion->prepare($sqlTop);
$stmt3->execute($params);
$topProductos = $stmt3->fetchAll(PDO::FETCH_ASSOC);

// 3b) Ventas por mes (dos métricas: cantidad y monto total)
$sqlMes = "
    SELECT date_trunc('month', pe.fecha) AS mes,
           SUM(pd.cantidad) AS cantidad_vendida,
           SUM(pd.subtotal) AS subtotal_total,
           SUM(pe.total) AS total_ventas
    FROM pedidodetalle pd
    JOIN pedidos pe ON pd.pedido_id = pe.id
    JOIN producto p ON pd.producto_id = p.id
";
$params = [];
if (!$isAll) {
    $sqlMes .= " WHERE p.categoria_id = :categoria ";
    $params[':categoria'] = $categoria;
}
$sqlMes .= " GROUP BY mes ORDER BY mes;";
$stmt4 = $conexion->prepare($sqlMes);
$stmt4->execute($params);
$ventasPorMes = $stmt4->fetchAll(PDO::FETCH_ASSOC);

// Totales
$totalVentas = 0; $totalItbms = 0;
foreach ($ventas as $r) {
    $totalVentas += floatval($r['total_general'] ?? 0);
    $totalItbms += floatval($r['itbms_total'] ?? 0);
}

// ---------- RESPUESTA JSON (para PDF en cliente) ---------- //
if ($formato === 'json') {
    header('Content-Type: application/json');
    echo json_encode([
        'ventas' => $ventas,
        'stock' => $stock,
        'totales' => [
            'total_ventas' => $totalVentas,
            'total_itbms' => $totalItbms,
            'total_general' => $totalVentas + $totalItbms
        ],
        'graficas' => [
            'top_productos' => $topProductos,
            'ventas_mes' => array_map(function($r){
                return [
                    'mes' => $r['mes'],
                    'cantidad_vendida' => intval($r['cantidad_vendida']),
                    'total_ventas' => floatval($r['total_ventas'])
                ];
            }, $ventasPorMes)
        ]
    ]);
    exit();
}

// ---------- GENERAR EXCEL (.xlsx) con PhpSpreadsheet ---------- //
if ($formato === 'xlsx') {
    $spreadsheet = new Spreadsheet();

    // Hoja 1: Ventas
    $sheetVentas = $spreadsheet->getActiveSheet();
    $sheetVentas->setTitle('Ventas');
    $sheetVentas->fromArray(['Producto','Cantidad Vendida','Subtotal','ITBMS','Total'], NULL, 'A1');

    $row = 2;
    foreach ($ventas as $v) {
        $sheetVentas->setCellValue("A{$row}", $v['nombre']);
        $sheetVentas->setCellValue("B{$row}", intval($v['cantidad_vendida']));
        $sheetVentas->setCellValue("C{$row}", floatval($v['subtotal_total']));
        $sheetVentas->setCellValue("D{$row}", floatval($v['itbms_total']));
        $sheetVentas->setCellValue("E{$row}", floatval($v['total_general']));
        $row++;
    }

    // Hoja 2: Stock
    $sheetStock = new Worksheet($spreadsheet, 'Stock');
    $spreadsheet->addSheet($sheetStock, 1);
    $sheetStock->fromArray(['Producto','Stock Disponible','Precio Unitario','Valor Total'], NULL, 'A1');
    $r = 2;
    foreach ($stock as $s) {
        $sheetStock->setCellValue("A{$r}", $s['nombre']);
        $sheetStock->setCellValue("B{$r}", intval($s['cantidad_disponible']));
        $sheetStock->setCellValue("C{$r}", floatval($s['precio_unitario']));
        $sheetStock->setCellValue("D{$r}", floatval($s['valor_total']));
        $r++;
    }

    // Hoja 3: Resumen (para gráficas)
    $sheetResumen = new Worksheet($spreadsheet, 'Resumen');
    $spreadsheet->addSheet($sheetResumen, 2);
    $sheetResumen->fromArray(['Métrica','Valor'], NULL, 'A1');

    // Resumen simple
    $sheetResumen->setCellValue('A2', 'Total Ventas');
    $sheetResumen->setCellValue('B2', $totalVentas);
    $sheetResumen->setCellValue('A3', 'Total ITBMS');
    $sheetResumen->setCellValue('B3', $totalItbms);
    $sheetResumen->setCellValue('A4', 'Total General');
    $sheetResumen->setCellValue('B4', $totalVentas + $totalItbms);

    // Además volcamos datos de Top Productos a la hoja 'Resumen' para gráficas
    $sheetResumen->setCellValue('D1', 'Producto');
    $sheetResumen->setCellValue('E1', 'Cantidad Vendida');
    $rr = 2;
    foreach ($topProductos as $tp) {
        $sheetResumen->setCellValue("D{$rr}", $tp['nombre']);
        $sheetResumen->setCellValue("E{$rr}", intval($tp['cantidad_vendida']));
        $rr++;
    }

    // Ventas por mes (también en Resumen)
    $sheetResumen->setCellValue('G1', 'Mes');
    $sheetResumen->setCellValue('H1', 'Cant. Vendida');
    $sheetResumen->setCellValue('I1', 'Total Ventas ($)');
    $rmm = 2;
    foreach ($ventasPorMes as $vm) {
        $mes = (new DateTime($vm['mes']))->format('Y-m'); // formato YYYY-MM
        $sheetResumen->setCellValue("G{$rmm}", $mes);
        $sheetResumen->setCellValue("H{$rmm}", intval($vm['cantidad_vendida']));
        $sheetResumen->setCellValue("I{$rmm}", floatval($vm['total_ventas']));
        $rmm++;
    }

    // ---------- Crear gráficos nativos de Excel ---------- //
    // 1) Gráfico: Top productos (columna)
    // Ubicación de los datos en Resumen: D2:D{rr-1} y E2:E{rr-1}
    $topEndRow = $rr - 1;
    if ($topEndRow >= 2) {
        $dataSeriesLabels = [ new DataSeriesValues('String', "Resumen!\$E\$1", null, 1) ];
        $xAxisTickValues = [ new DataSeriesValues('String', "Resumen!\$D\$2:\$D\${$topEndRow}", null, $topEndRow-1) ];
        $dataSeriesValues = [ new DataSeriesValues('Number', "Resumen!\$E\$2:\$E\${$topEndRow}", null, $topEndRow-1) ];

        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_CLUSTERED,
            range(0, count($dataSeriesValues)-1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );
        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title('Top productos (cantidad vendida)');

        $chart = new Chart(
            'chart1',
            $title,
            $legend,
            $plotArea,
            true,
            0,
            null,
            null
        );

        // posicionar el chart en hoja 'Resumen'
        $chart->setTopLeftPosition('A10');
        $chart->setBottomRightPosition('H25');
        $sheetResumen->addChart($chart);
    }

    // 2) Gráfico: Ventas por mes (línea) - datos en G2:G{rmm-1} y I2:I{rmm-1}
    $monthEndRow = $rmm - 1;
    if ($monthEndRow >= 2) {
        $dataSeriesLabels2 = [ new DataSeriesValues('String', "Resumen!\$I\$1", null, 1) ];
        $xAxisTickValues2 = [ new DataSeriesValues('String', "Resumen!\$G\$2:\$G\${$monthEndRow}", null, $monthEndRow-1) ];
        $dataSeriesValues2 = [ new DataSeriesValues('Number', "Resumen!\$I\$2:\$I\${$monthEndRow}", null, $monthEndRow-1) ];

        $series2 = new DataSeries(
            DataSeries::TYPE_LINECHART,
            null,
            range(0, count($dataSeriesValues2)-1),
            $dataSeriesLabels2,
            $xAxisTickValues2,
            $dataSeriesValues2
        );
        $plotArea2 = new PlotArea(null, [$series2]);
        $legend2 = new Legend(Legend::POSITION_RIGHT, null, false);
        $title2 = new Title('Ventas por mes (monto)');

        $chart2 = new Chart(
            'chart2',
            $title2,
            $legend2,
            $plotArea2,
            true,
            0,
            null,
            null
        );
        $chart2->setTopLeftPosition('I10');
        $chart2->setBottomRightPosition('P25');
        $sheetResumen->addChart($chart2);
    }

    // Ajustes finales: activar hoja Ventas como visible inicial
    $spreadsheet->setActiveSheetIndex(0);

    // Escritura y envío al navegador
    $filename = 'Reporte_Ventas_Stock_' . date('Ymd_His') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->setIncludeCharts(true);
    $writer->save('php://output');
    exit();
}

// Si no coincide formato conocido:
http_response_code(400);
echo json_encode(['error' => 'Formato no válido']);
exit();
