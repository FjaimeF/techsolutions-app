<?php include 'includes/header.php'; ?>
<link rel="stylesheet" href="assets/css/clientes.css">

<h1 style="margin-bottom: 20px; font-size: 24px;">Gestión de Clientes y Usuarios</h1>

<?php if(isset($_GET['success'])): ?>
    <div style="background: #d1fae5; color: #065f46; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <?php 
            if($_GET['success'] == 'banned') echo "Cliente suspendido correctamente.";
            if($_GET['success'] == 'activated') echo "Cliente reactivado correctamente.";
            if($_GET['success'] == 'deleted') echo "Cliente eliminado del sistema.";
        ?>
    </div>
<?php endif; ?>

<?php if(isset($_GET['error'])): ?>
    <div style="background: #fee2e2; color: #991b1b; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        <?php 
            if($_GET['error'] == 'self') echo "Error: No puedes banear o eliminar tu propia cuenta.";
            if($_GET['error'] == 'admin') echo "Error: No tienes permisos para modificar a otro Administrador.";
            if($_GET['error'] == 'db') echo "Error en la base de datos. Intenta de nuevo.";
        ?>
    </div>
<?php endif; ?>

<div class="table-container">
    <h3>Directorio de Clientes Registrados</h3>
    <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 15px;">Aquí puedes gestionar el acceso de los clientes que se registran desde la web.</p>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Consulta modificada: Solo mostramos a los usuarios con rol 'cliente'
            $stmt = $pdo->query("SELECT * FROM usuarios WHERE rol = 'cliente' ORDER BY id DESC");
            $usuarios = $stmt->fetchAll();

            if (count($usuarios) > 0) {
                foreach ($usuarios as $user) {
                    $estado_badge = $user['estado'] == 'activo' ? 'badge-success' : 'badge-danger';
                    ?>
                    <tr>
                        <td>#<?= $user['id'] ?></td>
                        <td><?= htmlspecialchars($user['nombre']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td><span class="badge" style="background:#e5e7eb; color:#374151;"><?= ucfirst($user['rol']) ?></span></td>
                        <td><span class="badge <?= $estado_badge ?>"><?= ucfirst($user['estado']) ?></span></td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <?php if ($user['estado'] == 'activo'): ?>
                                    <a href="clientes_process.php?accion=banear&id=<?= $user['id'] ?>" class="btn-action btn-warning" onclick="return confirm('¿Seguro que deseas banear a este cliente?');">
                                        <i class="fa-solid fa-ban"></i> Banear
                                    </a>
                                <?php else: ?>
                                    <a href="clientes_process.php?accion=activar&id=<?= $user['id'] ?>" class="btn-action btn-success">
                                        <i class="fa-solid fa-check"></i> Activar
                                    </a>
                                <?php endif; ?>

                                <a href="clientes_process.php?accion=eliminar&id=<?= $user['id'] ?>" class="btn-action btn-danger" onclick="return confirm('¿Estás seguro de eliminar permanentemente a este cliente?');">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;'>No hay clientes registrados aún.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>