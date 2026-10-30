<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}
$pedido_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Compra Exitosa</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background: var(--bg-color); display: flex; align-items: center; justify-content: center; height: 100vh;">
    <div style="background: white; padding: 50px; border-radius: 12px; text-align: center; max-width: 500px; box-shadow: 0 10px 25px rgba(0,0,0,0.1);">
        <i class="fa-solid fa-check-circle" style="font-size: 80px; color: var(--success); margin-bottom: 20px;"></i>
        <h1>¡Compra Exitosa!</h1>
        <p style="color: var(--text-muted); margin: 20px 0;">Tu pedido <strong>#<?= $pedido_id ?></strong> ha sido procesado correctamente.</p>
        <p style="color: var(--text-muted); font-size: 14px; margin-bottom: 30px;">El stock de los productos ha sido actualizado automáticamente.</p>
        <a href="tienda.php" class="btn-primary" style="text-decoration: none; padding: 12px 25px;">Volver a la Tienda</a>
    </div>
</body>
</html>