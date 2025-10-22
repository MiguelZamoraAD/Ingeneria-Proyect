document.addEventListener("DOMContentLoaded", () => {
    const modal = document.getElementById("modal-reporte");
    const btnReporte = document.getElementById("btn-generar-reporte");
    const btnCancelar = document.getElementById("cancelar-reporte");
    const btnConfirmar = document.getElementById("confirmar-reporte");

    btnReporte.addEventListener("click", () => {
        modal.style.display = "flex";
    });

    btnCancelar.addEventListener("click", () => {
        modal.style.display = "none";
    });

    btnConfirmar.addEventListener("click", async() => {
        modal.style.display = "none";

        try {
            const response = await fetch("../func/reporteVentasStock.php");
            const data = await response.json();

            const { ventas, stock } = data;
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();

            let y = 15;
            const marginX = 10;
            const pageWidth = 190;

            // Encabezado
            doc.setFontSize(16);
            doc.text("Reporte de Ventas y Stock", 105, y, null, null, "center");
            y += 10;
            doc.setLineWidth(0.5);
            doc.line(marginX, y, marginX + pageWidth, y); // Línea debajo del título
            y += 6;

            // === Sección de Ventas ===
            doc.setFontSize(13);
            doc.text("Ventas Generales", marginX, y);
            y += 6;

            // Encabezado de tabla Ventas
            doc.setFontSize(11);
            doc.rect(marginX, y, pageWidth, 8); // Marco encabezado
            doc.text("Producto", marginX + 2, y + 6);
            doc.text("Cant.", marginX + 65, y + 6);
            doc.text("Subtotal", marginX + 90, y + 6);
            doc.text("ITBMS", marginX + 125, y + 6);
            doc.text("Total", marginX + 160, y + 6);
            y += 8;

            ventas.forEach(item => {
                const nombre = item.nombre;
                const cantidad = parseInt(item.cantidad_vendida) || 0;
                const subtotal = parseFloat(item.subtotal_total) || 0;
                const itbms = parseFloat(item.itbms_total) || 0;
                const total = parseFloat(item.total_general || item.total) || 0;

                // Marco por fila
                doc.rect(marginX, y, pageWidth, 8);

                // Texto dentro del marco
                doc.text(nombre.slice(0, 28), marginX + 2, y + 6);
                doc.text(String(cantidad), marginX + 70, y + 6);
                doc.text(`$${subtotal.toFixed(2)}`, marginX + 90, y + 6);
                doc.text(`$${itbms.toFixed(2)}`, marginX + 125, y + 6);
                doc.text(`$${total.toFixed(2)}`, marginX + 160, y + 6);

                y += 8;
                if (y > 270) {
                    doc.addPage();
                    y = 15;
                }
            });

            y += 10;
            doc.setFontSize(13);
            doc.text("Inventario / Stock Actual", marginX, y);
            y += 6;

            // Encabezado tabla Stock
            doc.setFontSize(11);
            doc.rect(marginX, y, pageWidth, 8);
            doc.text("Producto", marginX + 2, y + 6);
            doc.text("Stock", marginX + 70, y + 6);
            doc.text("Precio U.", marginX + 110, y + 6);
            doc.text("Valor Total", marginX + 150, y + 6);
            y += 8;

            stock.forEach(item => {
                const nombre = item.nombre;
                const stockDisponible = parseInt(item.cantidad_disponible) || 0;
                const precio = parseFloat(item.precio_unitario) || 0;
                const valor = parseFloat(item.valor_total) || 0;

                doc.rect(marginX, y, pageWidth, 8);
                doc.text(nombre.slice(0, 30), marginX + 2, y + 6);
                doc.text(String(stockDisponible), marginX + 70, y + 6);
                doc.text(`$${precio.toFixed(2)}`, marginX + 110, y + 6);
                doc.text(`$${valor.toFixed(2)}`, marginX + 150, y + 6);

                y += 8;
                if (y > 270) {
                    doc.addPage();
                    y = 15;
                }
            });

            // === Pie de página ===
            const fecha = new Date().toISOString().slice(0, 10);
            y += 10;
            doc.setFontSize(10);
            doc.text(`Generado el: ${fecha}`, marginX, y);
            doc.text("Sistema de Gestión de Tienda", 140, y);

            // Descargar PDF
            doc.save(`Reporte_${fecha}.pdf`);
        } catch (error) {
            console.error("Error generando el reporte:", error);
            alert("Ocurrió un error al generar el reporte. Revisa la consola.");
        }
    });
});