<?php
session_start();

// Cabeceras para evitar que el navegador guarde en caché los datos
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header('Content-Type: application/json');

require_once __DIR__ . '/../config/database.php';

try {
    // 1. Contar productos totales
    $total_prod = $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();

    // 2. Contar productos con stock bajo o agotado
    $stock_critico = $pdo->query("SELECT COUNT(*) FROM productos WHERE stock <= stock_minimo")->fetchColumn();

    // 3. Contar pedidos del día (si no hay, devuelve 0)
    $pedidos_dia = $pdo->query("SELECT COUNT(*) FROM pedidos WHERE DATE(fecha) = CURDATE()")->fetchColumn();

    // 4. Sumar ventas del mes (si no hay, devuelve 0)
    $ventas_mes = $pdo->query("SELECT SUM(total) FROM pedidos WHERE MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())")->fetchColumn();
    $ventas_mes = $ventas_mes ? $ventas_mes : 0;

    // 5. Alertas de stock (Traer los productos más críticos)
    $stmt = $pdo->query("SELECT nombre, stock, codigo FROM productos WHERE stock <= stock_minimo ORDER BY stock ASC LIMIT 4");
    $alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver todo en formato JSON
    echo json_encode([
        'total_productos' => $total_prod,
        'stock_critico' => $stock_critico,
        'pedidos_dia' => $pedidos_dia,
        'ventas_mes' => number_format($ventas_mes, 2),
        'alertas' => $alertas
    ]);
    exit();

} catch (PDOException $e) {
    // Si hay un error, lo devolvemos como JSON
    echo json_encode(['error' => 'Error en la base de datos: ' . $e->getMessage()]);
    exit();
}
?>