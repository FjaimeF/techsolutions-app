<?php 
// VALIDACIÓN DE SEGURIDAD: Admin y SubAdmin pueden entrar
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['user_rol']) || ($_SESSION['user_rol'] != 'admin' && $_SESSION['user_rol'] != 'subadmin')) {
    header("Location: dashboard.php?error=no_access");
    exit();
}

include 'includes/header.php'; 
?>
<link rel="stylesheet" href="assets/css/empleados.css">

<h1 style="margin-bottom: 20px; font-size: 24px;">Gestión de Empleados</h1>

<!-- Mensajes de alerta -->
<?php if(isset($_GET['success'])): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <?php 
            if($_GET['success'] == 1) echo "Empleado registrado correctamente.";
            if($_GET['success'] == 2) echo "Empleado eliminado.";
        ?>
    </div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <?php 
            if($_GET['error'] == 1) echo "Por favor, llena todos los campos.";
            if($_GET['error'] == 2) echo "Error: El correo ya está registrado en el sistema.";
            if($_GET['error'] == 3) echo "Error al guardar en la base de datos.";
            if($_GET['error'] == 'denied') echo "No tienes permisos para realizar esta acción.";
        ?>
    </div>
<?php endif; ?>

<!-- FORMULARIO: Solo visible para el Admin real -->
<?php if ($_SESSION['user_rol'] == 'admin'): ?>
    <div class="form-container">
        <h3>Registrar Nuevo Empleado</h3>
        <form action="empleados_process.php" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label>Nombre Completo</label>
                    <input type="text" name="nombre" placeholder="Ej. Ana Torres" required>
                </div>
                <div class="form-group">
                    <label>DNI</label>
                    <input type="text" name="dni" placeholder="Ej. 44968217" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label>Cargo</label>
                    <select name="cargo" required>
                        <option value="">Seleccione un cargo...</option>
                        <option value="Administrador">Administrador</option>
                        <option value="SubAdministrador">SubAdministrador</option>
                        <option value="Vendedor">Vendedor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Email Corporativo</label>
                    <input type="email" name="email" placeholder="empleado@techsolutions.pe" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group" style="max-width: 350px;">
                    <label>Contraseña de Acceso</label>
                    <input type="password" name="password" placeholder="Crea una clave" required minlength="6">
                </div>
            </div>
            <button type="submit" class="btn-primary" style="max-width: 200px;">Guardar Empleado</button>
        </form>
    </div>
<?php else: ?>
    <!-- Si es SubAdmin, le mostramos un aviso -->
    <div style="background: #e0f2fe; color: #0369a1; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
        <i class="fa-solid fa-circle-info"></i> Estás en modo de solo lectura. Solo el Administrador principal puede crear o eliminar empleados.
    </div>
<?php endif; ?>

<div class="table-container" style="margin-top: 30px;">
    <h3>Lista de Empleados</h3>
    <table>
        <thead>
            <tr>
                <th>DNI</th>
                <th>Empleado</th>
                <th>Cargo</th>
                <th>Email</th>
                <?php if ($_SESSION['user_rol'] == 'admin'): ?>
                    <th>Acción</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $stmt = $pdo->query("SELECT * FROM empleados ORDER BY id DESC");
            $empleados = $stmt->fetchAll();

            if (count($empleados) > 0) {
                foreach ($empleados as $emp) {
                    // Lógica de colores simplificada
                    $badge_color = 'badge-success';
                    if ($emp['cargo'] == 'Vendedor') {
                        $badge_color = 'badge-warning';
                    }
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($emp['dni']) ?></td>
                        <td><?= htmlspecialchars($emp['nombre']) ?></td>
                        <td><span class="badge <?= $badge_color ?>"><?= htmlspecialchars($emp['cargo']) ?></span></td>
                        <td><?= htmlspecialchars($emp['email']) ?></td>
                        
                        <?php if ($_SESSION['user_rol'] == 'admin'): ?>
                            <td>
                                <a href="empleados_process.php?accion=eliminar&id=<?= $emp['id'] ?>" class="btn-action btn-danger" onclick="return confirm('¿Seguro que deseas eliminar a este empleado?');">
                                    <i class="fa-solid fa-trash"></i> Eliminar
                                </a>
                            </td>
                        <?php endif; ?>
                    </tr>
                    <?php
                }
            } else {
                $colspan = ($_SESSION['user_rol'] == 'admin') ? 5 : 4;
                echo "<tr><td colspan='$colspan' style='text-align:center;'>No hay empleados registrados aún.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>