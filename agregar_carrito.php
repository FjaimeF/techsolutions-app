<?php
session_start();
require 'config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'cliente') {
    echo json_encode(['status' => 'error', 'message' => 'No autorizado']);
    exit();
}

if (isset($_POST['id'])) {
    $id_producto = intval($_POST['id']);
    
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    if (isset($_SESSION['carrito'][$id_producto])) {
        $_SESSION['carrito'][$id_producto]++; 
    } else {
        $_SESSION['carrito'][$id_producto] = 1; 
    }

    // Contar cuántos productos hay en el carrito
    $cantidad_carrito = 0;
    foreach ($_SESSION['carrito'] as $cantidad) {
        $cantidad_carrito += $cantidad;
    }

    // Devolver éxito y la nueva cantidad
    echo json_encode([
        'status' => 'success', 
        'cart_count' => $cantidad_carrito,
        'message' => 'Producto agregado'
    ]);
    exit();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Falta el ID del producto']);
    exit();
}
?>