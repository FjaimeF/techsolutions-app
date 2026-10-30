<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cliente - TechSolutions</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="login-body">

    <div class="login-card" style="max-width: 500px;">
        <h2>Crear Cuenta</h2>
        <p>Regístrate para poder realizar tus pedidos</p>

        <!-- Mostrar mensajes de error o éxito -->
        <?php if(isset($_GET['error'])): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                <?php 
                    if($_GET['error'] == 1) echo "Por favor, llena todos los campos obligatorios.";
                    if($_GET['error'] == 2) echo "Este correo ya está registrado. Intenta con otro.";
                    if($_GET['error'] == 3) echo "Error al registrar. Intenta de nuevo.";
                ?>
            </div>
        <?php endif; ?>
        
        <?php if(isset($_GET['success'])): ?>
            <div style="background: #d1fae5; color: #065f46; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
                ¡Cuenta creada con éxito! <a href="index.php" style="color: #065f46; font-weight: bold;">Inicia sesión aquí</a>.
            </div>
        <?php endif; ?>

        <form action="registro_process.php" method="POST">
            <div class="form-group">
                <label>Nombre Completo</label>
                <input type="text" name="nombre" placeholder="Ej. Juan Pérez" required>
            </div>
            
            <div class="form-group" style="display: flex; gap: 15px;">
                <div style="flex: 1;">
                    <label>DNI / RUC</label>
                    <input type="text" name="dni" placeholder="Ej. 12345678" required>
                </div>
                <div style="flex: 1;">
                    <label>Teléfono</label>
                    <input type="text" name="telefono" placeholder="Ej. 987 654 321" required>
                </div>
            </div>

            <div class="form-group">
                <label>Correo Electrónico</label>
                <input type="email" name="email" placeholder="correo@ejemplo.com" required>
            </div>

            <div class="form-group">
                <label>Contraseña</label>
                <input type="password" name="password" placeholder="Crea una contraseña segura" required minlength="6">
            </div>

            <button type="submit" class="btn-primary">Registrarse</button>
            
            <div style="margin-top: 20px; font-size: 14px; color: var(--text-muted);">
                ¿Ya tienes una cuenta? 
                <a href="index.php" style="color: var(--primary-color); font-weight: 600; text-decoration: none;">Inicia sesión</a>
            </div>
        </form>
    </div>

</body>
</html>