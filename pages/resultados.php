<?php
require_once __DIR__ . '/class/productoCRUD.php';

$productos = new ProductoCrud();
$busqueda = $_GET['q'] ?? '';
$busqueda = trim($busqueda);

//busqueda en la base de datos
$resultados = [];
if (!empty($busqueda)) {
    //parametros de busqueda
    $res = $productos->listar(1000,1,$busqueda);
    $resultados = $res['data'] ?? [];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados de búsqueda</title>
    <link rel="stylesheet" href="resources/css/style.css">
</head>
<body>
    <h1>Resultados para "<?php echo htmlspecialchars($busqueda); ?>"</h1>

    <?php if (count($resultados) > 0): ?>
        <div class="product-grid">
            <?php foreach($resultados as $prod): ?>
                <div class="product-card">
                    <img src="<?php echo $prod['imagen_url']; ?>" alt="<?php echo $prod['nombre']; ?>">
                    <h3><?php echo $prod['nombre']; ?></h3>
                    <p><?php echo $prod['descripcion']; ?></p>
                    <p>Precio: $<?php echo $prod['precio']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>No se encontraron productos.</p>
    <?php endif; ?>
</body>
</html>
