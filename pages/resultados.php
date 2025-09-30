<?php
session_start();
require_once __DIR__ . '/../class/productoCRUD.php';

//Varibales y parametros
$productos = new ProductoCrud();
$busqueda = $_GET['q'] ?? '';
$busqueda = trim($busqueda);

//Buscar productos en la base de datos
$resultados = [];
if (!empty($busqueda)) {
    $res = $productos->listar(1000, 1, $busqueda);
    $resultados = $res['data'] ?? [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de búsqueda - MelodyMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/style.css">
</head>
<body>
    <?php include('../resources/include/header.php'); ?>

    <main class="container">
        <section class="search-results-section">
            <h1>Resultados para "<?php echo htmlspecialchars($busqueda); ?>"</h1>

            <?php if (count($resultados) > 0): ?>
                <div class="product-grid">
                    <?php foreach($resultados as $prod): ?>
                        <div class="product-card">
                            <?php if (!empty($prod['imagen_url'])): ?>
                                <img src="<?php echo $prod['imagen_url']; ?>" alt="<?php echo $prod['nombre']; ?>">
                            <?php else: ?>
                                <img src="../resources/img/default-product.png" alt="Sin imagen">
                            <?php endif; ?>
                            <h3><?php echo $prod['nombre']; ?></h3>
                            <p><?php echo $prod['descripcion']; ?></p>
                            <p class="precio">Precio: $<?php echo $prod['precio']; ?></p>
                            <p class="cantidad">Stock: <?php echo $prod['cantidad']; ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-results">No se encontraron productos para tu búsqueda.</p>
            <?php endif; ?>
        </section>
    </main>

    <?php include('../resources/include/footer.php'); ?>
</body>
</html>
