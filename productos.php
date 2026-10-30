<?php 
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_rol']) || ($_SESSION['user_rol'] != 'admin' && $_SESSION['user_rol'] != 'subadmin')) {
    header("Location: dashboard.php?error=no_access");
    exit();
}
include 'includes/header.php'; 
?>
<link rel="stylesheet" href="assets/css/productos.css">

<h1 style="margin-bottom: 20px; font-size: 24px;">Gestión de Productos</h1>

<!-- Mensajes de éxito o error -->
<?php if(isset($_GET['success'])): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <?php 
            if($_GET['success'] == 1) echo "¡Producto registrado exitosamente en el sistema!";
            if($_GET['success'] == 'deleted') echo "¡Producto eliminado correctamente del inventario!";
            if($_GET['success'] == 'stock_updated') echo "¡Stock actualizado correctamente!";
        ?>
    </div>
<?php endif; ?>
<?php if(isset($_GET['error'])): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <?php 
            if($_GET['error'] == 2) echo "Error: El código de producto ya existe. Intenta con otro.";
            if($_GET['error'] == 'db') echo "Error en la base de datos.";
            if($_GET['error'] == 'denied') echo "No tienes permisos para realizar esta acción.";
        ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <h3>Registrar Nuevo Producto</h3>
    <form action="productos_process.php" method="POST">
        <div class="form-row">
            <div class="form-group">
                <label>Código</label>
                <input type="text" name="codigo" placeholder="Ej. LAP-003" required>
            </div>
            <div class="form-group">
                <label>Nombre del Producto</label>
                <input type="text" name="nombre" placeholder="Ej. Laptop Asus VivoBook 15" required>
            </div>
            <div class="form-group">
                <label>Categoría</label>
                <select name="categoria" required>
                    <option value="">Seleccione...</option>
                    <option value="Laptops">Laptops</option>
                    <option value="Celulares">Celulares</option>
                    <option value="Audio">Audio</option>
                    <option value="Monitores">Monitores</option>
                    <option value="Accesorios">Accesorios</option>
                </select>
            </div>
        </div>
        <div class="form-row">
            <div class="form-group">
                <label>Precio (S/)</label>
                <input type="number" step="0.01" name="precio" placeholder="0.00" required>
            </div>
            <div class="form-group">
                <label>Stock Inicial</label>
                <input type="number" name="stock" placeholder="Ej. 10" required>
            </div>
            <div class="form-group">
                <label>Stock Mínimo <small style="color:var(--text-muted); font-size:10px;">(Alerta)</small></label>
                <input type="number" name="stock_minimo" placeholder="Ej. 3" required>
            </div>
        </div>
        <button type="submit" class="btn-primary" style="max-width: 200px;">Guardar Producto</button>
    </form>
</div>

<div class="table-container" style="margin-top: 30px;">
    <h3>Inventario Actual</h3>
    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Precio</th>
                <th>Stock Rápido</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
            $productos = $stmt->fetchAll();

            if (count($productos) > 0) {
                foreach ($productos as $prod) {
                    $badge_class = 'badge-success';
                    $estado_texto = 'Disponible';
                    
                    if ($prod['stock'] == 0) {
                        $badge_class = 'badge-danger';
                        $estado_texto = 'Agotado';
                    } elseif ($prod['stock'] <= $prod['stock_minimo']) {
                        $badge_class = 'badge-warning';
                        $estado_texto = 'Stock Bajo';
                    }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($prod['codigo']) ?></td>
                        <td><?= htmlspecialchars($prod['nombre']) ?></td>
                        <td>S/ <?= number_format($prod['precio'], 2) ?></td>
                        
                        <!-- FORMULARIO RÁPIDO PARA ACTUALIZAR STOCK -->
                        <td>
                            <form action="productos_process.php" method="POST" style="display: flex; gap: 5px; align-items: center;">
                                <input type="hidden" name="accion" value="actualizar_stock">
                                <input type="hidden" name="id" value="<?= $prod['id'] ?>">
                                <input type="number" name="nuevo_stock" value="<?= $prod['stock'] ?>" style="width: 60px; padding: 5px; border: 1px solid #d1d5db; border-radius: 4px;">
                                <button type="submit" style="background: var(--success); color: white; border: none; padding: 5px 8px; border-radius: 4px; cursor: pointer;" title="Actualizar Stock">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                            </form>
                        </td>

                        <td><span class="badge <?= $badge_class ?>"><?= $estado_texto ?></span></td>
                        <td>
                            <a href="productos_process.php?accion=eliminar&id=<?= $prod['id'] ?>" class="btn-action btn-danger" onclick="return confirm('¿Estás seguro de eliminar este producto?');">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;'>No hay productos registrados aún.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>