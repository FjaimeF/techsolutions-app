document.addEventListener("DOMContentLoaded", function() {
    
    function actualizarDashboard() {
        // TRUCO ANTI-CACHÉ: Agregamos un timestamp para forzar la recarga
        const timestamp = new Date().getTime();
        
        fetch('api/metricas.php?t=' + timestamp)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la red o archivo no encontrado');
                }
                return response.json();
            })
            .then(data => {
                // Si el servidor devolvió un error, lo mostramos
                if (data.error) {
                    console.error("Error del servidor:", data.error);
                    return;
                }

                // Actualizar Tarjetas del Dashboard
                if(document.getElementById('metric-productos')) {
                    document.getElementById('metric-productos').innerText = data.total_productos;
                    document.getElementById('metric-critico').innerText = data.stock_critico;
                    document.getElementById('metric-pedidos').innerText = data.pedidos_dia;
                    document.getElementById('metric-ventas').innerText = 'S/ ' + data.ventas_mes;
                }

                // Actualizar Alertas de Stock
                const contenedor = document.getElementById('contenedor-alertas');
                if(contenedor) {
                    if (data.alertas.length === 0) {
                        contenedor.innerHTML = '<p style="color: var(--success);">Todo el inventario está en niveles óptimos.</p>';
                    } else {
                        let html = '<div style="display: flex; flex-direction: column; gap: 10px;">';
                        data.alertas.forEach(prod => {
                            let color = prod.stock == 0 ? 'var(--danger)' : 'var(--warning)';
                            let texto = prod.stock == 0 ? 'Agotado' : 'Stock Bajo';
                            html += `
                                <div style="display: flex; justify-content: space-between; padding: 10px; border-left: 4px solid ${color}; background: var(--bg-color);">
                                    <span><strong>${prod.codigo}</strong> - ${prod.nombre}</span>
                                    <span class="badge" style="background: ${color}; color: white;">${texto} (${prod.stock} und.)</span>
                                </div>
                            `;
                        });
                        html += '</div>';
                        contenedor.innerHTML = html;
                    }
                }
            })
            .catch(error => console.error('Error de Fetch:', error));
    }

    // Ejecutar al cargar la página
    actualizarDashboard();

    // Ejecutar cada 5 segundos
    setInterval(actualizarDashboard, 5000);
});