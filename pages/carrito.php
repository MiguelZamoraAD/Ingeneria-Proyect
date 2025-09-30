<!-- <?php
session_start();
require_once __DIR__ . '/../class/Usuarios.php';
$usuario = new UsuarioLogin();

// Comprobar si el usuario está autenticado
if (isset($_SESSION['autenticado']) && $_SESSION['autenticado'] === 'SI') {
    // La lógica de la sesión se mantiene
    //var_dump($_SESSION['tipo'] ?? null);
} else {
    // Lógica para usuarios no logueados
}
//Archivo para mostrar productos
 
?>-->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tienda Rápida - Pago y Escaneo</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- ZXing para escaneo de códigos de barras -->
    <script src="https://cdn.jsdelivr.net/npm/@zxing/library@0.19.1/umd/index.min.js"></script>
    
    <!-- Firebase SDK -->
    <script type="module">
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
        import { getAuth, signInAnonymously, signInWithCustomToken } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";
        import { getFirestore } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-firestore.js";

        const appId = typeof __app_id !== 'undefined' ? __app_id : 'default-app-id';
        const firebaseConfig = typeof __firebase_config !== 'undefined' ? JSON.parse(__firebase_config) : null;
        const initialAuthToken = typeof __initial_auth_token !== 'undefined' ? __initial_auth_token : null;

        let app, db, auth, userId;

        async function initFirebase() {
            if (!firebaseConfig) {
                console.error("ERROR: Configuración de Firebase no disponible.");
                document.getElementById('mensaje-escaneo').textContent = "ERROR: Configuración de DB no disponible.";
                return;
            }

            try {
                app = initializeApp(firebaseConfig);
                db = getFirestore(app);
                auth = getAuth(app);
                
                if (initialAuthToken) {
                    await signInWithCustomToken(auth, initialAuthToken);
                } else {
                    await signInAnonymously(auth);
                }
                
                userId = auth.currentUser?.uid || crypto.randomUUID();
                
                // Inicializa la app
                window.App.init(db, userId);
            } catch (error) {
                console.error("Error al inicializar o autenticar Firebase:", error);
                document.getElementById('mensaje-escaneo').textContent = "ERROR de autenticación. Ver consola.";
            }
        }

        window.onload = initFirebase;
    </script>

    <!-- Estilos -->
    <link rel="stylesheet" href="../resources/css/carrito.css">

</head>
<body>
    <header>
        <h1>🛍️ Tienda Express - TPV</h1>
    </header>

    <main class="contenedor-principal">
        <!-- SECCIÓN DE ESCANER Y CÁMARA -->
        <section class="seccion-escaner lg:col-span-2">
            <h2>Escanear Producto</h2>
            <div id="area-camara">
                <video id="video-scanner" playsinline></video>
                <p class="instruccion">Apunta la cámara al código de barras.</p>
                <p id="mensaje-escaneo" class="hidden"></p>
            </div>
            <div id="escaneo-manual" class="mt-4">
                <p class="text-gray-500 mb-2">O introduce el código manualmente:</p>
                <div class="flex space-x-2">
                    <input type="text" id="input-manual" placeholder="Ej: 123456789012" class="flex-grow p-2 border rounded-lg">
                    <button id="btn-agregar-manual" class="w-auto px-4 bg-indigo-500 text-white hover:bg-indigo-600">Añadir</button>
                </div>
            </div>
        </section>

        <!-- SECCIÓN DEL CARRITO -->
        <section class="seccion-carrito lg:col-span-1">
            <h2>Mi Carrito</h2>
            <ul id="lista-carrito" class="max-h-80 overflow-y-auto mb-4"></ul>
            <div class="resumen-carrito">
                <p class="text-xl font-bold">Total: <span id="total-carrito">$0.00</span></p>
                <button id="btn-proceder-pago" disabled>Proceder al Pago</button>
                <div id="mensaje-sistema" class="text-sm mt-3 p-2 bg-yellow-100 text-yellow-800 rounded hidden"></div>
            </div>
        </section>
        
        <!-- SECCIÓN DE PAGO (Inicialmente oculta) -->
        <section class="seccion-pago lg:col-span-3" style="display: none;"> 
            <div class="max-w-md mx-auto">
                <h2 class="text-center">Confirmación y Pago</h2>
                <p class="text-center text-2xl mb-6">Total a pagar: <strong id="total-pago-final" class="text-indigo-700">$0.00</strong></p>
                
                <form id="formulario-pago">
                    <p class="font-medium mb-3">Selecciona el Método de Pago:</p>
                    <div class="metodos-pago space-y-2">
                        <label>
                            <input type="radio" name="metodo-pago" value="tarjeta" required class="mr-2 text-indigo-600">
                            <span>💳 Tarjeta de Crédito/Débito</span>
                        </label>
                        <label>
                            <input type="radio" name="metodo-pago" value="efectivo" class="mr-2 text-indigo-600">
                            <span>💵 Efectivo</span>
                        </label>
                        <label>
                            <input type="radio" name="metodo-pago" value="transferencia" class="mr-2 text-indigo-600">
                            <span>🏦 Transferencia Bancaria</span>
                        </label>
                    </div>

                    <div id="detalles-tarjeta" class="mt-4 p-4 border rounded-lg bg-gray-50" style="display: none;">
                        <label for="numero-tarjeta" class="block font-medium mb-1">Número de Tarjeta (Simulado):</label>
                        <input type="text" id="numero-tarjeta" placeholder="XXXX XXXX XXXX XXXX" class="p-2 border rounded-lg w-full">
                    </div>

                    <button type="submit" id="btn-finalizar-compra" class="mt-6">
                        Finalizar Compra y Actualizar DB
                    </button>
                    <button type="button" id="btn-volver-carrito">
                        Volver al Carrito
                    </button>
                </form>
            </div>
        </section>

    </main>

    <!-- JS externo -->
    <script src="app.js" type="module"></script>
</body>
</html>
