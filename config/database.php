<?php
// Detectamos si estamos en local (Laragon) o en Azure
$es_local = ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1');

if ($es_local) {
    // --- DATOS DE TU LARAGON LOCAL ---
    $host = "localhost";
    $dbname = "techsolutions";
    $username = "root";
    $password = "";
} else {
    // --- DATOS DE TU AZURE MYSQL ---
    $host = "mysql-techsolutions-jaime.mysql.database.azure.com";
    $dbname = "techsolutions";
    $username = "fjaimef";
    $password = "Yoloyolo5"; // <--- CAMBIA ESTO por tu contraseña real
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Si estamos en Azure, activamos el SSL (Azure lo requiere)
    if (!$es_local) {
        $pdo->setAttribute(PDO::MYSQL_ATTR_SSL_CA, '/home/site/wwwroot/DigiCertGlobalRootG2.crt.pem');
    }
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>