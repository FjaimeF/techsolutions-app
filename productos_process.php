<?php
session_start();
require 'config/database.php';

// Validar que sea admin o subadmin
if (!isset($_SESSION['user_id']) || ($_SESSION['user_rol'] != 'admin' && $_SESSION['user_rol'] != 'subadmin')) {
    header("Location: dashboard.php");
    exit();
}

// --- LÓGICA PARA GUARDAR PRODUCTO NUEVO ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['codigo'])) {
    $codigo = trim($_POST['codigo']);
    $nombre = trim($_POST['nombre']);
    $categoria = trim($_POST['categoria']);
    $precio = floatval($_POST['precio']);
    $stock = intval($_POST['stock']);
    $stock_minimo = intval($_POST['stock_minimo']);

    if (empty($codigo) || empty($nombre) || empty($categoria)) {
        header("Location: productos.php?error=1");
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO productos (codigo, nombre, categoria, precio, stock, stock_minimo) VALUES (:codigo, :nombre, :categoria, :precio, :stock, :stock_minimo)");
        $stmt->execute([
            'codigo' => $codigo,
            'nombre' => $nombre,
            'categoria' => $categoria,
            'precio' => $precio,
            'stock' => $stock,
            'stock_minimo' => $stock_minimo
        ]);

        header("Location: productos.php?success=1");
        exit();

    } catch (PDOException $e) {
        header("Location: productos.php?error=2");
        exit();
    }
}

// --- LÓGICA PARA ACTUALIZAR STOCK RÁPIDO ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accion']) && $_POST['accion'] == 'actualizar_stock') {
    $id = intval($_POST['id']);
    $nuevo_stock = intval($_POST['nuevo_stock']);

    try {
        $stmt = $pdo->prepare("UPDATE productos SET stock = :stock WHERE id = :id");
        $stmt->execute(['stock' => $nuevo_stock, 'id' => $id]);
        header("Location: productos.php?success=stock_updated");
        exit();
    } catch (PDOException $e) {
        header("Location: productos.php?error=db");
        exit();
    }
}

// --- LÓGICA PARA ELIMINAR PRODUCTO ---
if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM productos WHERE id = :id");
        $stmt->execute(['id' => $id]);
        header("Location: productos.php?success=deleted");
        exit();
    } catch (PDOException $e) {
        header("Location: productos.php?error=db");
        exit();
    }
}
?>