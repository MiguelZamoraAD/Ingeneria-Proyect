// @ts-nocheck
document.addEventListener("DOMContentLoaded", () => {
    const supabase = window.supabase ? window.supabase.createClient(supabaseUrl, supabaseKey) : null;
    const modal = document.getElementById("modal-reporte");
    const btnReporte = document.getElementById("btn-generar-reporte");
    const btnCancelar = document.getElementById("cancelar-reporte");
    const btnConfirmar = document.getElementById("confirmar-reporte");

    if (btnReporte) {
        btnReporte.addEventListener("click", () => {
            modal.style.display = "flex";
        });
    }
    if (btnCancelar) {
        btnCancelar.addEventListener("click", () => {
            modal.style.display = "none";
        });
    }

    if (btnConfirmar) {
        btnConfirmar.addEventListener("click", async() => {
            modal.style.display = "none";
            const categoria = document.getElementById('categoria').value; // 'todas' o id de categoria
            const formato = document.getElementById('formato').value; // 'pdf' o 'xlsx'

            if (formato === 'xlsx') {
                // Para Excel: redirigir al endpoint que generará y servirá el XLSX.
                // Usamos GET para que el navegador descargue el archivo directamente.
                const url = `../func/reporteVentasStock.php?formato=xlsx&categoria=${encodeURIComponent(categoria)}`;
                window.location.href = url;
                return;
            }

            // Si formato === 'pdf', obtenemos JSON desde backend y generamos PDF en cliente (jsPDF)
            try {
                const url = `../func/reporteVentasStock.php?formato=json&categoria=${encodeURIComponent(categoria)}`;
                const res = await fetch(url);
                if (!res.ok) throw new Error('Error en la petición al servidor');
                const data = await res.json();

                // Estructura esperada: {ventas: [...], stock: [...], totales: {...}, graficas: {...}}
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
                doc.line(marginX, y, marginX + pageWidth, y);
                y += 6;

                // Ventas (tabla compacta)
                doc.setFontSize(13);
                doc.text("Ventas Generales", marginX, y);
                y += 6;
                doc.setFontSize(11);
                doc.rect(marginX, y, pageWidth, 8);
                doc.text("Producto", marginX + 2, y + 6);
                doc.text("Cant.", marginX + 65, y + 6);
                doc.text("Subtotal", marginX + 90, y + 6);
                doc.text("ITBMS", marginX + 125, y + 6);
                doc.text("Total", marginX + 160, y + 6);
                y += 8;

                ventas.forEach(item => {
                    const nombre = item.nombre || '';
                    const cantidad = parseInt(item.cantidad_vendida) || 0;
                    const subtotal = parseFloat(item.subtotal_total) || 0;
                    const itbms = parseFloat(item.itbms_total) || 0;
                    const total = parseFloat(item.total_general || item.total) || 0;

                    doc.rect(marginX, y, pageWidth, 8);
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

                // Stock
                y += 10;
                doc.setFontSize(13);
                doc.text("Inventario / Stock Actual", marginX, y);
                y += 6;
                doc.setFontSize(11);
                doc.rect(marginX, y, pageWidth, 8);
                doc.text("Producto", marginX + 2, y + 6);
                doc.text("Stock", marginX + 70, y + 6);
                doc.text("Precio U.", marginX + 110, y + 6);
                doc.text("Valor Total", marginX + 150, y + 6);
                y += 8;

                stock.forEach(item => {
                    const nombre = item.nombre || '';
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

                // Pie
                const fecha = new Date().toISOString().slice(0, 10);
                y += 10;
                doc.setFontSize(10);
                doc.text(`Generado el: ${fecha}`, marginX, y);
                doc.text("Sistema de Gestión de Tienda", 140, y);

                doc.save(`Reporte_${fecha}.pdf`);
            } catch (err) {
                console.error('Error generando reporte PDF:', err);
                alert('Ocurrió un error al generar el PDF. Revisa la consola.');
            }
        });
    }
});
//
//
//
//
document.addEventListener("DOMContentLoaded", async() => {
    // --- CLIENTE SUPABASE ---
    const supabase = window.supabase ? window.supabase.createClient(supabaseUrl, supabaseKey) : null;

    // --- ELEMENTOS DEL HISTORIAL ---
    const btnHistorial = document.getElementById("btn-historial");
    const modalHistorial = document.createElement("div");
    modalHistorial.id = "modal-historial";
    Object.assign(modalHistorial.style, {
        display: "none",
        position: "fixed",
        top: "0",
        left: "0",
        width: "100%",
        height: "100%",
        background: "rgba(0,0,0,0.6)",
        zIndex: "2000",
        justifyContent: "center",
        alignItems: "center"
    });

    modalHistorial.innerHTML = `
        <div id="historial-contenido" style="background:#fff; color:#333; border-radius:10px; width:600px; max-height:80vh; overflow-y:auto; padding:20px; position:relative;">
            <h3 style="text-align:center; margin-bottom:10px;">🧾 Historial de Compras</h3>
            <button id="cerrar-historial" style="position:absolute; top:10px; right:10px; background:red; color:#fff; border:none; border-radius:5px; padding:5px 10px; cursor:pointer;">X</button>
            <div id="historial-lista" style="margin-top:15px;"></div>
        </div>
    `;
    document.body.appendChild(modalHistorial);

    const cerrarHistorialBtn = modalHistorial.querySelector("#cerrar-historial");
    const historialLista = modalHistorial.querySelector("#historial-lista");

    // --- FUNCIÓN: OBTENER SESIÓN PHP ---
    const obtenerSesion = async() => {
        try {
            const res = await fetch("/inge/func/sessionJSON.php");
            const data = await res.json();

            if (!data.ok) {
                historialLista.innerHTML = "<p style='color:red; text-align:center;'>Debes iniciar sesión para ver tu historial.</p>";
                return null;
            }
            return data.correo;
        } catch (error) {
            console.error("Error al obtener sesión:", error);
            historialLista.innerHTML = "<p style='color:red; text-align:center;'>Error al verificar sesión.</p>";
            return null;
        }
    };

    // --- EVENTO: ABRIR HISTORIAL ---
    if (btnHistorial) {
        btnHistorial.addEventListener("click", async(e) => {
            e.preventDefault();
            modalHistorial.style.display = "flex";
            historialLista.innerHTML = "<p style='text-align:center;'>Cargando historial...</p>";

            // Obtener correo desde sesión PHP
            const correoUsuario = await obtenerSesion();
            if (!correoUsuario) return;

            try {
                // --- CONSULTA DE HISTORIAL DE COMPRAS ---
                const { data: pedidos, error: errorPedidos } = await supabase
                    .from("pedidos")
                    .select("id, fecha, subtotal, itbms, total")
                    .eq("correo_usuario", correoUsuario)
                    .order("fecha", { ascending: false });

                if (errorPedidos) throw errorPedidos;

                if (!pedidos || pedidos.length === 0) {
                    historialLista.innerHTML = "<p style='text-align:center;'>No tienes compras registradas.</p>";
                    return;
                }

                historialLista.innerHTML = ""; // Limpiar antes de agregar resultados

                // --- Para cada pedido, obtener sus detalles ---
                for (const pedido of pedidos) {
                    const { data: detalles, error: errorDetalles } = await supabase
                        .from("pedidodetalle")
                        .select(`
                            cantidad,
                            subtotal,
                            nombre,
                            producto:producto(imagen_url, precio)
                        `)
                        .eq("pedido_id", pedido.id);

                    if (errorDetalles) console.error(errorDetalles);

                    const detallesHTML = (detalles || []).map(det => {
                        const imagen = det.producto.imagen_url || "https://via.placeholder.com/60";
                        return `
                            <div style="display:flex; align-items:center; gap:10px; border-bottom:1px solid #ddd; padding:8px 0;">
                                <img src="${imagen}" alt="img" style="width:60px; height:60px; border-radius:8px; object-fit:cover;">
                                <div style="flex:1;">
                                    <strong>${det.nombre}</strong><br>
                                    Cantidad: ${det.cantidad}<br>
                                    Precio: $${(det.producto?.precio || 0).toFixed(2)}<br>
                                    Subtotal: $${det.subtotal.toFixed(2)}
                                </div>
                            </div>
                        `;
                    }).join("");

                    historialLista.innerHTML += `
                        <div style="background:#fafafa; border-radius:10px; margin-bottom:15px; padding:10px; box-shadow:0 2px 5px rgba(0,0,0,0.1);">
                            <h4 style="margin-bottom:5px;"><!--🧾 Pedido #${pedido.id}--></h4>
                            <p style="margin:0 0 5px 0;">Fecha: ${new Date(pedido.fecha).toLocaleString()}</p>
                            <div>${detallesHTML}</div>
                            <div style="text-align:right; margin-top:10px;">
                                <strong>Subtotal:</strong> $${pedido.subtotal.toFixed(2)}<br>
                                <strong>ITBMS:</strong> $${pedido.itbms.toFixed(2)}<br>
                                <strong>Total:</strong> $${pedido.total.toFixed(2)}
                            </div>
                        </div>
                    `;
                }
            } catch (err) {
                console.error(err);
                historialLista.innerHTML = "<p style='color:red; text-align:center;'>Error al cargar el historial.</p>";
            }
        });
    }

    // --- CERRAR MODAL HISTORIAL ---
    if (cerrarHistorialBtn) {
        cerrarHistorialBtn.addEventListener("click", () => {
            modalHistorial.style.display = "none";
        });
    }

    // --- CERRAR MODAL AL HACER CLIC FUERA DEL CUADRO ---
    modalHistorial.addEventListener("click", (e) => {
        if (e.target === modalHistorial) {
            modalHistorial.style.display = "none";
        }
    });
});