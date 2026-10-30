<?php
session_start();
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['user_rol'] == 'admin' || $_SESSION['user_rol'] == 'empleado') {
        header("Location: dashboard.php");
    } else {
        header("Location: tienda.php");
    }
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - TechSolutions</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="login-body">

    <div class="login-card">
        <h2>TechSolutions</h2>
        <p>Sistema de Gestión de Inventarios y Pedidos</p>

        <?php if(isset($_GET['error'])): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                <?php 
                    if($_GET['error'] == 1) echo "Por favor, llena todos los campos.";
                    if($_GET['error'] == 2) echo "Correo o contraseña incorrectos.";
                    if($_GET['error'] == 3) echo "Tu cuenta ha sido suspendida. Contacta al administrador.";
                ?>
            </div>
        <?php endif; ?>

        <form action="login_process.php" method="POST">
            <div class="form-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="admin@techsolutions.pe" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Ingresa tu contraseña" required>
            </div>
            <button type="submit" class="btn-primary">Ingresar</button>
            
            <div style="margin-top: 20px; font-size: 14px; color: var(--text-muted);">
                ¿No tienes una cuenta? 
                <a href="registro.php" style="color: var(--primary-color); font-weight: 600; text-decoration: none;">Regístrate aquí</a>
            </div>
        </form>
    </div>

</body>
</html>