<?php include 'includes/header.php'; ?>

<!-- NUEVO: Alerta de acceso denegado -->
<?php if(isset($_GET['error']) && $_GET['error'] == 'no_access'): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
        <i class="fa-solid fa-lock"></i> <strong>Acceso Denegado:</strong> No tienes permisos de Administrador para ver esa sección.
    </div>
<?php endif; ?>

<h1 style="margin-bottom: 20px; font-size: 24px;">Inicio</h1>

<div class="metrics-grid">
    <div class="metric-card">
        <h3>Total Productos</h3>
        <div class="value" id="metric-productos">0</div>
    </div>
    <div class="metric-card">
        <h3>Stock Crítico</h3>
        <div class="value" id="metric-critico" style="color: var(--warning);">0</div>
    </div>
    <div class="metric-card">
        <h3>Pedidos del Día</h3>
        <div class="value" id="metric-pedidos">0</div>
    </div>
    <div class="metric-card">
        <h3>Ventas del Mes</h3>
        <div class="value" id="metric-ventas">S/ 0.00</div>
    </div>
</div>

<!-- Sección de Alertas en Tiempo Real -->
<div class="table-container">
    <h3 style="margin-bottom: 15px; color: var(--danger);"><i class="fa-solid fa-triangle-exclamation"></i> Alertas de Stock (Reposición inmediata)</h3>
    <div id="contenedor-alertas">
        <p style="color: var(--text-muted); font-size: 14px;">Consultando base de datos...</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>