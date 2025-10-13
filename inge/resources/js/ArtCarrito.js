document.addEventListener("DOMContentLoaded", () => {
    const btnPago = document.getElementById("btn-proceder-pago");
    const seccionCarrito = document.querySelector(".seccion-carrito");
    const seccionPago = document.querySelector(".seccion-pago");
    const btnVolver = document.getElementById("btn-volver-carrito");
    const totalFinal = document.getElementById("total-pago-final");
    const totalCarrito = document.getElementById("total-carrito");

    // Habilitar botón si hay productos
    if (parseFloat(totalCarrito.textContent.replace('$', '')) > 0) {
        btnPago.disabled = false;
    }

    // Mostrar sección de pago
    btnPago.addEventListener("click", () => {
        seccionCarrito.style.display = "none";
        seccionPago.style.display = "block";
        totalFinal.textContent = totalCarrito.textContent;
    });

    // Volver al carrito
    btnVolver.addEventListener("click", () => {
        seccionPago.style.display = "none";
        seccionCarrito.style.display = "block";
    });

    // Mostrar detalles de tarjeta
    document.querySelectorAll("input[name='metodo-pago']").forEach(input => {
        input.addEventListener("change", e => {
            document.getElementById("detalles-tarjeta").style.display =
                e.target.value === "tarjeta" ? "block" : "none";
        });
    });

    // Simular pago y vaciar carrito
    document.getElementById("formulario-pago").addEventListener("submit", e => {
        e.preventDefault();
        alert("✅ Compra realizada con éxito. Gracias por tu pedido!");
        window.location.href = "../func/vaciarCarrito.php";
    });
});