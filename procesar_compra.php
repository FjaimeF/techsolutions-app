<?php
session_start();
require 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'cliente') {
    header("Location: index.php");
    exit();
}

if (!isset($_SESSION['carrito']) || count($_SESSION['carrito']) == 0) {
    header("Location: carrito.php");
    exit();
}

// 1. Buscar el ID del cliente en la tabla 'clientes' usando el ID del usuario
$stmt = $pdo->prepare("SELECT id FROM clientes WHERE email = (SELECT email FROM usuarios WHERE id = :user_id)");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$cliente_data = $stmt->fetch();

if (!$cliente_data) {
    // Si no se encuentra, lo creamos automáticamente para que la compra no falle
    $stmt_user = $pdo->prepare("SELECT nombre, email FROM usuarios WHERE id = :user_id");
    $stmt_user->execute(['user_id' => $_SESSION['user_id']]);
    $u = $stmt_user->fetch();
    
    $stmt_insert = $pdo->prepare("INSERT INTO clientes (nombre, dni_ruc, email) VALUES (:nombre, '00000000', :email)");
    $stmt_insert->execute(['nombre' => $u['nombre'], 'email' => $u['email']]);
    
    $cliente_id = $pdo->lastInsertId();
} else {
    $cliente_id = $cliente_data['id'];
}

// 2. Calcular el total y verificar stock
$total_pagar = 0;
$productos_a_comprar = [];

foreach ($_SESSION['carrito'] as $id_prod => $cantidad) {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
    $stmt->execute(['id' => $id_prod]);
    $prod = $stmt->fetch();

    if ($prod['stock'] < $cantidad) {
        die("Error: No hay suficiente stock para el producto: " . $prod['nombre'] . ". Por favor, actualiza tu carrito.");
    }

    $subtotal = $prod['precio'] * $cantidad;
    $total_pagar += $subtotal;
    $productos_a_comprar[] = [
        'id' => $id_prod,
        'cantidad' => $cantidad,
        'precio' => $prod['precio']
    ];
}

// 3. Insertar el Pedido y restar el stock
try {
    $pdo->beginTransaction();

    // Insertar cabecera del pedido
    $stmt = $pdo->prepare("INSERT INTO pedidos (cliente_id, total, estado) VALUES (:cliente_id, :total, 'Pagado')");
    $stmt->execute(['cliente_id' => $cliente_id, 'total' => $total_pagar]);
    $pedido_id = $pdo->lastInsertId();

    // Insertar detalles y restar stock
    foreach ($productos_a_comprar as $item) {
        $stmt_detalle = $pdo->prepare("INSERT INTO detalle_pedidos (pedido_id, producto_id, cantidad, precio_unitario) VALUES (:pedido_id, :producto_id, :cantidad, :precio)");
        $stmt_detalle->execute([
            'pedido_id' => $pedido_id,
            'producto_id' => $item['id'],
            'cantidad' => $item['cantidad'],
            'precio' => $item['precio']
        ]);

        // RESTA DE STOCK
        $stmt_stock = $pdo->prepare("UPDATE productos SET stock = stock - :cantidad WHERE id = :id");
        $stmt_stock->execute(['cantidad' => $item['cantidad'], 'id' => $item['id']]);
    }

    $pdo->commit();

    unset($_SESSION['carrito']);
    header("Location: compra_exitosa.php?id=" . $pedido_id);
    exit();

} catch (Exception $e) {
    $pdo->rollBack();
    die("Error al procesar la compra: " . $e->getMessage());
}
?>