<?php
// Detectamos si estamos en local (Laragon) o en Azure
$es_local = ($_SERVER['SERVER_NAME'] == 'localhost' || $_SERVER['SERVER_NAME'] == '127.0.0.1');

if ($es_local) {
    // --- DATOS DE TU LARAGON LOCAL ---
    $host = "localhost";
    $dbname = "techsolutions";
    $username = "root";
    $password = "";
    $options = []; // Sin opciones SSL para local
} else {
    // --- DATOS DE TU AZURE MYSQL ---
    $host = "mysql-techsolutions-jaime.mysql.database.azure.com";
    $dbname = "techsolutions";
    $username = "fjaimef";
    $password = "Yoloyolo5"; 

    // Opciones para forzar SSL en Azure (Soluciona el error 3159)
    $options = [
        PDO::MYSQL_ATTR_SSL_CA => '/home/site/wwwroot/DigiCertGlobalRootG2.crt.pem',
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];
}

try {
    // Creamos la conexión pasando las opciones (si es local, el arreglo está vacío)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>