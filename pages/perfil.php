<?php include('../resources/include/header.php')?>
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - MelodyMart</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../resources/css/style.css">
    <link rel="stylesheet" href="../resources/css/perfil.css">
</head>

<body>

    <main>
        <section class="profile-section">
            <div class="container profile-container">
                <div class="profile-header">
                    <h2>Información de Mi Perfil</h2>
                    <p>Gestiona tu información personal y de contacto.</p>
                </div>
                <div class="profile-info">
                    <div class="info-group">
                        <label for="name">Nombre Completo:</label>
                        <p id="name">Juan Pérez</p>
                    </div>
                    <div class="info-group">
                        <label for="email">Correo Electrónico:</label>
                        <p id="email">juan.perez@email.com</p>
                    </div>
                    <div class="info-group">
                        <label for="phone">Teléfono:</label>
                        <p id="phone">+52 55 1234 5678</p>
                    </div>
                    <div class="info-group">
                        <label for="address">Dirección de Envío:</label>
                        <p id="address">Calle Falsa 123, Ciudad de México, México</p>
                    </div>
                </div>
                <div class="profile-actions">
                    <button>Editar Información</button>
                </div>
            </div>
        </section>
    </main>

    <script src="../resources/js/perfil.js"></script>
</body>

</html>
<?php include('../resources/include/footer.php')?>