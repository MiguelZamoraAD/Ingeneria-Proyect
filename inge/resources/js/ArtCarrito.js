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

<<<<<<< Updated upstream:inge/resources/js/ArtCarrito.js
    // Simular pago y vaciar carrito
    document.getElementById("formulario-pago").addEventListener("submit", e => {
        e.preventDefault();
        alert("✅ Compra realizada con éxito. Gracias por tu pedido!");
        window.location.href = "../func/procesarPedido.php";
    });

=======
    // Simular pago y vaciar carrito hacer arreglo y que se vea en la pantalla y que el usaurio pueda descargar el recibo
    document.getElementById("formulario-pago").addEventListener("submit", e => {
        e.preventDefault();
        Swal.fire({
            icon: 'success',
            title: 'Pedido procesado',
            text: '✅ Compra realizada con éxito. Se descargara su recibo de compra;'
        });

        generarReciboPDF();
        setTimeout(() => {
            window.location.href = "../func/procesarPedido.php";
        });
    });

    //funcion para generar el recibo en PDF
    function generarReciboPDF() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        let y = 20;
        doc.setFontSize(18);
        doc.text("RECIBO DE COMPRA", 20, y);
        y += 10;
        doc.setFontSize(12);
        doc.text("Volumen Brutal - Tienda de CDs", 20, y);
        y += 10;
        doc.text("Fecha: " + new Date().toLocaleString(), 20, y);
        y += 10;
        doc.text("====================================", 20, y);
        y += 10;
        // Obtener los productos del carrito desde HTML
        document.querySelectorAll(".item-carrito").forEach((item) => {
            const nombre = item.querySelector("h3").textContent;
            const cantidad = item.querySelector(".cantidad").textContent;
            const precio = item.dataset.precio;
            const subtotal = item.querySelector(".subtotal").textContent;
            doc.text(`Producto: ${nombre}`, 20, y);
            y += 7;
            doc.text(`Cantidad: ${cantidad}`, 20, y);
            y += 7;
            doc.text(`Precio unitario: $${precio}`, 20, y);
            y += 7;
            if (y > 270) { // Nueva página si se llena
                doc.addPage();
                y = 20;
            }
        });
        doc.text("====================================", 20, y);
        y += 10;
        const total = document.getElementById("total-carrito").textContent;
        const subtotal = document.getElementById("subtotal-carrito").textContent;
        const itbms = document.getElementById("itbms-carrito").textContent;
        doc.setFontSize(13);
        doc.text(`Subtotal: ${subtotal}`, 20, y);
        y += 8;
        doc.text(`ITBMS (7%): ${itbms}`, 20, y);
        y += 8;
        doc.setFontSize(14);
        doc.text(`TOTAL FINAL: ${total}`, 20, y);
        y += 10;
        // Guardar PDF
        doc.save("recibo_compra.pdf");
    }

>>>>>>> Stashed changes:resources/js/ArtCarrito.js
    // Manejar botones de sumar y restar
    document.querySelectorAll(".item-carrito").forEach(item => {
        const id = item.dataset.id;
        const cantidadEl = item.querySelector(".cantidad");
        const subtotalEl = item.querySelector(".subtotal");
        const precioUnitario = parseFloat(item.dataset.precio); // precio por producto
<<<<<<< Updated upstream:inge/resources/js/ArtCarrito.js

=======
>>>>>>> Stashed changes:resources/js/ArtCarrito.js
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
<<<<<<< Updated upstream:inge/resources/js/ArtCarrito.js
            const stock = parseInt(item.dataset.stock); // stock del producto
            let cantidadActual = parseInt(cantidadEl.textContent);

=======
            const stock = parseInt(item.dataset.stock);
            let cantidadActual = parseInt(cantidadEl.textContent);
            console.log("Stock disponible:", stock);
>>>>>>> Stashed changes:resources/js/ArtCarrito.js
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
<<<<<<< Updated upstream:inge/resources/js/ArtCarrito.js
                            alert(data.msg); // mensaje de stock
=======
                            Swal.fire({
                                icon: 'warning',
                                title: 'Stock insuficiente',
                                text: data.msg
                            }); // mensaje de stock
>>>>>>> Stashed changes:resources/js/ArtCarrito.js
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
<<<<<<< Updated upstream:inge/resources/js/ArtCarrito.js
=======
    actualizarTotales();
>>>>>>> Stashed changes:resources/js/ArtCarrito.js

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