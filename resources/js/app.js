// Definición del objeto global para la lógica de la aplicación
window.App = (function() {
    let codeReader;
    let videoElement = document.getElementById('video-scanner');
    let carrito = [];
    let dbInstance;
    let userId;
    let lastScanTime = 0;
    const SCAN_DEBOUNCE_MS = 2000;

    const MOCK_PRODUCTS = {
        "123456789012": { id: "prod_a1", name: "Manzana Roja", price: 0.99, stock: 50 },
        "987654321098": { id: "prod_b2", name: "Leche Entera (1L)", price: 1.75, stock: 100 },
        "112233445566": { id: "prod_c3", name: "Pan Integral", price: 2.40, stock: 30 },
        "445566778899": { id: "prod_d4", name: "Agua Mineral (500ml)", price: 0.50, stock: 200 },
    };

    const $listaCarrito = document.getElementById('lista-carrito');
    const $totalCarrito = document.getElementById('total-carrito');
    const $totalPagoFinal = document.getElementById('total-pago-final');
    const $btnProcederPago = document.getElementById('btn-proceder-pago');
    const $btnVolverCarrito = document.getElementById('btn-volver-carrito');
    const $seccionEscaner = document.querySelector('.seccion-escaner');
    const $seccionCarrito = document.querySelector('.seccion-carrito');
    const $seccionPago = document.querySelector('.seccion-pago');
    const $detallesTarjeta = document.getElementById('detalles-tarjeta');
    const $formularioPago = document.getElementById('formulario-pago');
    const $inputManual = document.getElementById('input-manual');
    const $btnAgregarManual = document.getElementById('btn-agregar-manual');
    const $mensajeEscaneo = document.getElementById('mensaje-escaneo');
    const $mensajeSistema = document.getElementById('mensaje-sistema');
    const $btnFinalizarCompra = document.getElementById('btn-finalizar-compra');

    // ---------------------- Funciones de Firebase/Supabase (simulación) ----------------------
    async function fetchProductByBarcode(barcode) {
        $mensajeEscaneo.textContent = "Buscando producto...";
        $mensajeEscaneo.classList.remove('hidden', 'bg-red-600', 'bg-green-600');
        $mensajeEscaneo.classList.add('bg-yellow-600');

        const product = MOCK_PRODUCTS[barcode];

        await new Promise(resolve => setTimeout(resolve, 500));

        if (product) {
            $mensajeEscaneo.textContent = `Producto encontrado: ${product.name}`;
            $mensajeEscaneo.classList.replace('bg-yellow-600', 'bg-green-600');
            return product;
        } else {
            $mensajeEscaneo.textContent = `Error: Producto con código ${barcode} no encontrado.`;
            $mensajeEscaneo.classList.replace('bg-yellow-600', 'bg-red-600');
            return null;
        }
    }

    async function registerTransactionAndReduceStock(items, total, metodoPago) {
        const transactionData = {
            userId: userId,
            timestamp: new Date().toISOString(),
            totalAmount: total,
            paymentMethod: metodoPago,
            items: items.map(item => ({ id: item.id, qty: item.quantity, price: item.price })),
            status: 'completed'
        };

        showSystemMessage("Procesando pago y actualizando inventario...", 'yellow');

        try {
            if (dbInstance) {
                await addDoc(collection(dbInstance, `/artifacts/default-app-id/public/data/transacciones`), transactionData);
            }

            items.forEach(item => console.log(`Simulando reducción de stock: ${item.name}, -${item.quantity}`));

            showSystemMessage(`¡Compra finalizada con éxito! Total: $${total.toFixed(2)}. Método: ${metodoPago}`, 'green');
            return true;
        } catch (error) {
            console.error("Error al registrar la transacción:", error);
            showSystemMessage("ERROR: No se pudo completar la transacción.", 'red');
            return false;
        }
    }

    // ---------------------- Escaneo ----------------------
    function startScanner() {
        if (typeof ZXing === 'undefined') {
            $mensajeEscaneo.textContent = "ERROR: Librería de escaneo no cargada.";
            $mensajeEscaneo.classList.remove('hidden');
            return;
        }

        codeReader = new ZXing.BrowserMultiFormatReader();
        codeReader.listVideoInputDevices()
            .then(devices => {
                const videoInputDevice = devices.find(d => d.label.toLowerCase().includes('back') || d.label.toLowerCase().includes('trasera')) || devices[0];
                if (videoInputDevice) {
                    codeReader.decodeFromVideoDevice(videoInputDevice.deviceId, videoElement, (result, err) => {
                        if (result) handleScanResult(result.text);
                    });
                    $mensajeEscaneo.textContent = "Listo para escanear...";
                    $mensajeEscaneo.classList.add('hidden');
                } else {
                    $mensajeEscaneo.textContent = "No se encontró cámara compatible.";
                    $mensajeEscaneo.classList.remove('hidden');
                }
            }).catch(err => {
                $mensajeEscaneo.textContent = "Error al acceder a la cámara. Revisa permisos.";
                $mensajeEscaneo.classList.remove('hidden');
            });
    }

    function stopScanner() {
        if (codeReader) {
            codeReader.reset();
        }
    }

    async function handleScanResult(barcode) {
        const now = Date.now();
        if (now - lastScanTime < SCAN_DEBOUNCE_MS) return;
        lastScanTime = now;

        const product = await fetchProductByBarcode(barcode);
        if (product) addItemToCart(product);
    }

    // ---------------------- Carrito ----------------------
    function addItemToCart(product) {
        const existing = carrito.find(i => i.id === product.id);
        if (existing) {
            existing.quantity += 1;
            showSystemMessage(`Añadido: +1 ${product.name}.`, 'blue');
        } else {
            carrito.push({...product, quantity: 1 });
            showSystemMessage(`Añadido: ${product.name} al carrito.`, 'blue');
        }
        renderCart();
    }

    function removeItem(id) {
        const index = carrito.findIndex(i => i.id === id);
        if (index > -1) {
            if (carrito[index].quantity > 1) carrito[index].quantity -= 1;
            else carrito.splice(index, 1);
        }
        renderCart();
    }

    function calculateTotal() { return carrito.reduce((sum, i) => sum + i.price * i.quantity, 0); }

    function renderCart() {
        $listaCarrito.innerHTML = '';
        const total = calculateTotal();

        if (carrito.length === 0) {
            $listaCarrito.innerHTML = '<li class="text-center text-gray-500 py-4">El carrito está vacío. ¡Escanea un producto!</li>';
            $btnProcederPago.disabled = true;
        } else {
            carrito.forEach(item => {
                const li = document.createElement('li');
                li.className = 'flex justify-between items-center py-2 border-b';
                li.innerHTML = `
                    <div class="flex-1 min-w-0">
                        <span class="font-medium">${item.name}</span>
                        <span class="text-sm text-gray-500 block">$${item.price.toFixed(2)} c/u</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="text-lg font-semibold text-indigo-700">${item.quantity}</span>
                        <button data-id="${item.id}" class="btn-remove text-red-500 hover:text-red-700 p-1 rounded-full bg-red-100 hover:bg-red-200">✖</button>
                    </div>
                `;
                $listaCarrito.appendChild(li);
            });
            $btnProcederPago.disabled = false;
        }

        $totalCarrito.textContent = `$${total.toFixed(2)}`;
        $totalPagoFinal.textContent = `$${total.toFixed(2)}`;

        document.querySelectorAll('.btn-remove').forEach(btn => btn.addEventListener('click', e => {
            e.stopPropagation();
            removeItem(e.currentTarget.dataset.id);
        }));
    }

    // ---------------------- UI / Flujo ----------------------
    function showSystemMessage(msg, type) {
        let classes;
        switch (type) {
            case 'blue':
                classes = 'bg-blue-100 text-blue-800';
                break;
            case 'green':
                classes = 'bg-green-100 text-green-800';
                break;
            case 'red':
                classes = 'bg-red-100 text-red-800';
                break;
            case 'yellow':
            default:
                classes = 'bg-yellow-100 text-yellow-800';
                break;
        }
        $mensajeSistema.textContent = msg;
        $mensajeSistema.className = `text-sm mt-3 p-2 rounded ${classes}`;
        $mensajeSistema.classList.remove('hidden');
        setTimeout(() => $mensajeSistema.classList.add('hidden'), 4000);
    }

    function switchToPayment() {
        if (carrito.length === 0) { showSystemMessage("El carrito está vacío.", 'yellow'); return; }
        stopScanner();
        $seccionEscaner.style.display = 'none';
        $seccionCarrito.style.display = 'none';
        $seccionPago.style.display = 'block';
    }

    function switchToCart() {
        $seccionPago.style.display = 'none';
        $seccionEscaner.style.display = 'block';
        $seccionCarrito.style.display = 'block';
        startScanner();
    }

    function handlePaymentMethodChange(e) {
        $detallesTarjeta.style.display = e.target.value === 'tarjeta' ? 'block' : 'none';
    }

    async function handleFinalizePurchase(e) {
        e.preventDefault();
        const metodoPago = new FormData($formularioPago).get('metodo-pago');
        const total = calculateTotal();

        if (!metodoPago) { showSystemMessage("Selecciona un método de pago.", 'red'); return; }

        $btnFinalizarCompra.disabled = true;
        $btnFinalizarCompra.textContent = 'Procesando...';

        const success = await registerTransactionAndReduceStock(carrito, total, metodoPago);

        $btnFinalizarCompra.disabled = false;
        $btnFinalizarCompra.textContent = 'Finalizar Compra';

        if (success) {
            carrito = [];
            renderCart();
            setTimeout(switchToCart, 3000);
        }
    }

    // ---------------------- Inicialización ----------------------
    function init(db, authUserId) {
        dbInstance = db;
        userId = authUserId;

        $btnProcederPago.addEventListener('click', switchToPayment);
        $btnVolverCarrito.addEventListener('click', switchToCart);
        $formularioPago.addEventListener('submit', handleFinalizePurchase);
        document.querySelectorAll('input[name="metodo-pago"]').forEach(r => r.addEventListener('change', handlePaymentMethodChange));
        $btnAgregarManual.addEventListener('click', () => {
            const barcode = $inputManual.value.trim();
            if (barcode) {
                handleScanResult(barcode);
                $inputManual.value = '';
            } else { showSystemMessage("Introduce un código válido.", 'yellow'); }
        });

        renderCart();
        startScanner();
    }

    return { init, startScanner, stopScanner, renderCart };
})();