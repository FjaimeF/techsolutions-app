<?php
require_once __DIR__ . '/../config/database.php';

try {
    $stmt = $pdo->query("SELECT id, nombre, categoria, codigo, precio, stock, stock_minimo FROM productos ORDER BY id DESC");
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    header('Content-Type: application/json');
    echo json_encode($productos);
    exit();

} catch (PDOException $e) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error en la base de datos']);
    exit();
}
?>