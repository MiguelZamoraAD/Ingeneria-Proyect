<?php 
session_start();
require_once __DIR__ . '/../class/Usuarios.php';

$usuario = new UsuarioLogin();
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== 'SI') {
    header('Location: session.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro de Producto - MelodyMart</title>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../resources/css/product.css">
</head>
<body>
<header>
  <div class="container">
    <div class="logo"><a href="../index.php">MelodyMart 🎶</a></div>
    <nav>
      <ul>
        <li><a href="producto.php">Productos</a></li>
        <?php if ($_SESSION['tipo']==='adm'): ?>
          <li><a href="#">Agregar nuevos productos</a></li>
        <?php endif; ?>
        <li><a href="#categorias">Categorías</a></li>
        <li><a href="#novedades">Novedades</a></li>
        <li class="user-profile">
          <a href="#" id="profile-link">Mi Perfil</a>
          <div class="profile-dropdown" id="profile-menu">
            <a href="perfil.php">Configuración</a>
            <a href="#">Historial de Compras</a>
            <a href="func/salir.php">Cerrar Sesión</a>
          </div>
        </li>
        <li class="cart-icon">
          <a href="#" id="cart-link">🛒 Carrito (<span id="cart-count">0</span>)</a>
        </li>
      </ul>
    </nav>
  </div>
</header>

<main>
  <section class="registro-section">
    <div class="container registro-container">
      <div class="registro-header">
        <h2>Registra un Nuevo Producto</h2>
        <p>Completa los campos para añadir un nuevo artículo a tu inventario.</p>
      </div>

      <div class="registro-form-container">
        <form id="productoForm" action="#" method="POST" class="registro-form" enctype="multipart/form-data">
          <div class="form-group">
            <label for="nombre">Nombre del Producto</label>
            <input type="text" id="nombre" name="nombre" required>
          </div>

          <div class="form-group">
            <label for="imagen">Imagen del Producto</label>
            <input type="file" id="imagen" name="imagen" accept="image/*">
            <div class="preview-container">
                <img id="preview" src="" alt="Miniatura" style="display:none; max-width:150px; margin-top:10px;">
            </div>
        </div>

          <div class="form-group">
            <label for="descripcion">Descripción</label>
            <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
          </div>

          <div class="form-group">
            <label for="precio">Precio</label>
            <input type="number" id="precio" name="precio" step="0.01" min="0" required>
          </div>

          <div class="form-group">
            <label for="cantidad">Cantidad en Stock</label>
            <input type="number" id="cantidad" name="cantidad" min="0" required>
          </div>

          <div class="form-group">
            <label for="artista">Seleccione el Artista</label>
            <select name="artista_id" id="artista" required>
              <option value="">Cargando artistas...</option>
            </select>
          </div>

          <div class="form-group">
            <label for="categoria">Seleccione la Categoría</label>
            <select name="categoria_id" id="categoria" required>
              <option value="">Cargando categorías...</option>
            </select>
          </div>

          <div class="form-actions">
            <button type="submit" class="btnRegistrar">Registrar Producto</button>
          </div>
        </form>
      </div>
    </div>
  </section>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/@supabase/supabase-js@2"></script>
<script src="../resources/js/producto.js"></script>
<script src="../resources/js/perfil.js"></script>
</body>
</html>
<?php include('../resources/include/footer.php')?>
