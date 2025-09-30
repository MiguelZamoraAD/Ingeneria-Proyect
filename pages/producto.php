<?php
session_start();
require_once __DIR__ . '/../class/productoCRUD.php';
require_once __DIR__ . '/../class/categoriaCRUD.php';


// Cargar categorías desde la base de datos
$productos = new ProductoCrud();
$categoriaCrud = new CategoriaCRUD();
$categorias = $categoriaCrud->listar();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MelodyMart - Productos</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/style.css">
</head>
<body>
    <?php include('../resources/include/header.php'); ?>

    <main class="container">
        <section class="hero-section hero-coleccion">
            <div class="hero-content">
                <h1>Descubre nuestros productos</h1>
                <div class="search-bar">
                    <input type="text" id="search-input" placeholder="Buscar productos...">
                </div>
                <!--/////categorías/////-->
                <section class="category-section">
                    <div class="category-buttons">
                        <button data-category="todos">Todos</button>
                        <?php foreach($categorias as $cat): ?>
                            <button data-category="<?php echo htmlspecialchars($cat['nombre']); ?>">
                                <?php echo htmlspecialchars($cat['nombre']); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                </section>
            </div>
        </section>

        <section id="productos-coleccion" class="product-section container">
            <div class="product-grid" id="product-list-coleccion">
            </div>
        </section>
    </main>
    <?php include('../resources/include/footer.php'); ?>

    <script>
        const searchInput = document.getElementById('search-input');
        const productGrid = document.getElementById('product-list-coleccion');
        const categoryButtons = document.querySelectorAll('.category-buttons button');

        //Funcion para cargar los productos de forma dinamica AJAX
        function cargarProductos(busqueda = '', categoria = '') {
            fetch(`ajax/buscarProductos.php?q=${encodeURIComponent(busqueda)}&categoria=${encodeURIComponent(categoria)}`)
                .then(res => res.json())
                .then(data => {
                    productGrid.innerHTML = '';
                    if(data.length > 0){
                        data.forEach(prod => {
                            productGrid.innerHTML += `
                                <div class="product-card">
                                    <img src="${prod.imagen_url || '../resources/img/default-product.png'}" alt="${prod.nombre}">
                                    <h3>${prod.nombre}</h3>
                                    <p>${prod.descripcion}</p>
                                    <p>Precio: $${prod.precio}</p>
                                    <p>Stock: ${prod.cantidad}</p>
                                </div>
                            `;
                        });
                    } else {
                        productGrid.innerHTML = '<p class="no-results">No se encontraron productos.</p>';
                    }
                });
        }

        //Buscar mientras escribes
        searchInput.addEventListener('input', () => {
            cargarProductos(searchInput.value);
        });

        //Filtrar por categoría
        categoryButtons.forEach(btn => {
            btn.addEventListener('click', () => {
                categoryButtons.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                cargarProductos(searchInput.value, btn.getAttribute('data-category'));
            });
        });

        //cargaar todos los productos
        cargarProductos();
    </script>
</body>
</html>