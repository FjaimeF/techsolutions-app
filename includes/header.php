<?php
require_once __DIR__ . '/../config/auth.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TechSolutions - Sistema</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
<div class="app-container">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-microchip"></i> TechSolutions
        </div>
        <nav class="sidebar-nav">
            <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>"><i class="fa-solid fa-house"></i> Inicio</a>
            <a href="clientes.php" class="<?= $current_page == 'clientes.php' ? 'active' : '' ?>"><i class="fa-solid fa-users"></i> Clientes</a>
            
            <!-- NUEVO: Admin y SubAdmin pueden ver Empleados -->
            <?php if ($_SESSION['user_rol'] == 'admin' || $_SESSION['user_rol'] == 'subadmin'): ?>
                <a href="empleados.php" class="<?= $current_page == 'empleados.php' ? 'active' : '' ?>"><i class="fa-solid fa-user-tie"></i> Empleados</a>
            <?php endif; ?>

            <a href="catalogo.php" class="<?= $current_page == 'catalogo.php' ? 'active' : '' ?>"><i class="fa-solid fa-store"></i> Catálogo</a>
            <a href="productos.php" class="<?= $current_page == 'productos.php' ? 'active' : '' ?>"><i class="fa-solid fa-box"></i> Productos</a>
            <a href="quienes-somos.php" class="<?= $current_page == 'quienes-somos.php' ? 'active' : '' ?>"><i class="fa-solid fa-circle-info"></i> Quiénes Somos</a>
        </nav>
    </aside>
    <main class="main-content">
        <header class="topbar">
            <div><h2 style="font-size: 18px;">Panel de Administración</h2></div>
            <div style="display: flex; align-items: center; gap: 15px;">
                <span style="font-size: 14px; font-weight: 500; text-transform: capitalize;">
                    <?= htmlspecialchars($_SESSION['user_nombre']) ?> (<?= htmlspecialchars($_SESSION['user_rol']) ?>)
                </span>
                <a href="logout.php" style="color: var(--danger); text-decoration: none; font-size: 14px;"><i class="fa-solid fa-arrow-right-from-bracket"></i> Salir</a>
            </div>
        </header>
        <div class="content-area">