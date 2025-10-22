document.addEventListener("DOMContentLoaded", () => {
    const btnPago = document.getElementById("btn-proceder-pago");
    const seccionCarrito = document.querySelector(".seccion-carrito");
    const seccionPago = document.querySelector(".seccion-pago");
    const btnVolver = document.getElementById("btn-volver-carrito");
    const totalFinal = document.getElementById("total-pago-final");
    const totalCarrito = document.getElementById("total-carrito");

    const ITBMS = 0.07; // 7%

    // Habilitar botón si hay productos
    if (parseFloat(totalCarrito.textContent.replace('$', '')) > 0) {
        btnPago.disabled = false;
    }

    // Mostrar sección de pago
    btnPago.addEventListener("click", () => {
        seccionCarrito.style.display = "none";
        seccionPago.style.display = "block";
        totalFinal.textContent = calcularTotalConItbms();
    });

    // Volver al carrito
    btnVolver.addEventListener("click", () => {
        seccionPago.style.display = "none";
        seccionCarrito.style.display = "block";
    });

    // Simular pago y vaciar carrito
    document.getElementById("formulario-pago").addEventListener("submit", e => {
        e.preventDefault();
        alert("✅ Compra realizada con éxito. Gracias por tu pedido!");
        window.location.href = "../func/procesarPedido.php";
    });

    // Manejar botones de sumar y restar
    document.querySelectorAll(".item-carrito").forEach(item => {
        const id = item.dataset.id;
        const cantidadEl = item.querySelector(".cantidad");
        const subtotalEl = item.querySelector(".subtotal");
        const precioUnitario = parseFloat(item.dataset.precio); // precio por producto

        // Botón restar
        item.querySelector(".btn-restar").addEventListener("click", () => {
            fetch("../func/actualizarCarrito.php", {
                    method: "POST",
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}&accion=restar`
                })
                .then(res => res.json())
                .then(data => {
                    if (data.ok) {
                        if (data.cantidad > 0) {
                            cantidadEl.textContent = data.cantidad;
                            subtotalEl.textContent = "$" + (precioUnitario * data.cantidad).toFixed(2);
                        } else {
                            item.remove();
                        }
                        actualizarTotales();
                    }
                });
        });

        // Botón sumar
        item.querySelector(".btn-sumar").addEventListener("click", () => {
            const stock = parseInt(item.dataset.stock); // stock del producto
            let cantidadActual = parseInt(cantidadEl.textContent);

            if (cantidadActual < stock) {
                fetch("../func/actualizarCarrito.php", {
                        method: "POST",
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: `id=${id}&accion=sumar`
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.ok) {
                            cantidadEl.textContent = data.cantidad;
                            subtotalEl.textContent = "$" + (precioUnitario * data.cantidad).toFixed(2);
                            actualizarTotales();
                        } else {
                            alert(data.msg); // mensaje de stock
                        }
                    });
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Stock alcanzado',
                    text: 'No puedes agregar más de lo que hay disponible.'
                });
            }
        });
    });

    // Función para actualizar subtotal, total y contador
    function actualizarTotales() {
        let subtotal = 0;
        let totalCantidad = 0;

        document.querySelectorAll(".item-carrito").forEach(item => {
            const cantidad = parseInt(item.querySelector(".cantidad").textContent) || 0;
            const precio = parseFloat(item.dataset.precio) || 0;
            subtotal += precio * cantidad;
            totalCantidad += cantidad;
        });

        const itbms = subtotal * ITBMS;
        const totalConItbms = subtotal + itbms;

        // Actualizar elementos en pantalla
        document.getElementById("subtotal-carrito").textContent = "$" + subtotal.toFixed(2);
        document.getElementById("itbms-carrito").textContent = "$" + itbms.toFixed(2);
        document.getElementById("total-carrito").textContent = "$" + totalConItbms.toFixed(2);
        document.getElementById("total-pago-final").textContent = "$" + totalConItbms.toFixed(2);
        document.getElementById("cart-count").textContent = totalCantidad;

        // Habilitar o deshabilitar botón de pago
        btnPago.disabled = totalCantidad === 0;
    }


    // Función para calcular total con ITBMS (para mostrar en pago)
    function calcularTotalConItbms() {
        let subtotal = 0;
        document.querySelectorAll(".item-carrito").forEach(item => {
            const cantidad = parseInt(item.querySelector(".cantidad").textContent) || 0;
            const precio = parseFloat(item.dataset.precio) || 0;
            subtotal += precio * cantidad;
        });
        return "$" + (subtotal * (1 + ITBMS)).toFixed(2);
    }

    // Inicializar totales al cargar
    actualizarTotales();
});