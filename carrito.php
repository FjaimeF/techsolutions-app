<?php
session_start();
require 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'cliente') {
    header("Location: index.php");
    exit();
}

// Lógica para quitar un producto del carrito
if (isset($_GET['remove'])) {
    $id_remove = intval($_GET['remove']);
    if (isset($_SESSION['carrito'][$id_remove])) {
        unset($_SESSION['carrito'][$id_remove]);
    }
    header("Location: carrito.php");
    exit();
}

// Calcular totales y preparar datos
$total_pagar = 0;
$items_carrito = [];

if (isset($_SESSION['carrito']) && count($_SESSION['carrito']) > 0) {
    foreach ($_SESSION['carrito'] as $id_prod => $cantidad) {
        // Consultar el producto real a la BD
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
        $stmt->execute(['id' => $id_prod]);
        $prod = $stmt->fetch();

        if ($prod) {
            $subtotal = $prod['precio'] * $cantidad;
            $total_pagar += $subtotal;
            
            $items_carrito[] = [
                'id' => $prod['id'],
                'nombre' => $prod['nombre'],
                'precio' => $prod['precio'],
                'cantidad' => $cantidad,
                'subtotal' => $subtotal
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito - TechSolutions</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="background: var(--bg-color);">

    <header class="topbar" style="background: var(--primary-color); color: white;">
        <div style="display: flex; align-items: center; gap: 10px;">
            <i class="fa-solid fa-microchip" style="font-size: 24px;"></i>
            <h2 style="font-size: 20px;">TechSolutions</h2>
        </div>
        <div>
            <a href="tienda.php" style="color: white; text-decoration: none; font-size: 14px; background: rgba(255,255,255,0.2); padding: 8px 15px; border-radius: 5px;">
                <i class="fa-solid fa-arrow-left"></i> Volver a la Tienda
            </a>
        </div>
    </header>

    <div style="max-width: 800px; margin: 40px auto; padding: 0 20px;">
        <h1 style="margin-bottom: 20px;">Mi Carrito de Compras</h1>

        <?php if (count($items_carrito) > 0): ?>
            <div class="table-container" style="background: white; padding: 20px; border-radius: 10px;">
                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items_carrito as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['nombre']) ?></td>
                                <td>S/ <?= number_format($item['precio'], 2) ?></td>
                                <td><?= $item['cantidad'] ?></td>
                                <td>S/ <?= number_format($item['subtotal'], 2) ?></td>
                                <td>
                                    <a href="carrito.php?remove=<?= $item['id'] ?>" style="color: var(--danger); text-decoration: none;">
                                        <i class="fa-solid fa-trash"></i> Quitar
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div style="text-align: right; margin-top: 30px;">
                    <h2 style="color: var(--primary-color);">Total a Pagar: S/ <?= number_format($total_pagar, 2) ?></h2>
                    <br>
                    <!-- Botón que procesará la compra -->
                    <a href="procesar_compra.php" class="btn-primary" style="display: inline-block; padding: 15px 30px; font-size: 16px; text-decoration: none; border-radius: 8px;">
                        <i class="fa-solid fa-credit-card"></i> Finalizar Compra
                    </a>
                </div>
            </div>
        <?php else: ?>
            <div style="background: white; padding: 40px; text-align: center; border-radius: 10px;">
                <i class="fa-solid fa-cart-shopping" style="font-size: 50px; color: #d1d5db; margin-bottom: 20px;"></i>
                <h3>Tu carrito está vacío</h3>
                <p style="color: var(--text-muted); margin-bottom: 20px;">Agrega productos desde el catálogo para continuar.</p>
                <a href="tienda.php" class="btn-primary" style="text-decoration: none; padding: 10px 20px;">Ir a la Tienda</a>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>