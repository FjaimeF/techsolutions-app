<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/database.php';

// 1. Verificar que haya iniciado sesión
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

// 2. Buscar su rol en la Base de Datos
$stmt = $pdo->prepare("SELECT rol, nombre FROM usuarios WHERE id = :id");
$stmt->execute(['id' => $_SESSION['user_id']]);
$usuario_actual = $stmt->fetch();

// 3. Si el usuario ya no existe, cerramos sesión
if (!$usuario_actual) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// 4. Actualizamos la sesión
$_SESSION['user_rol'] = $usuario_actual['rol'];
$_SESSION['user_nombre'] = $usuario_actual['nombre'];

// 5. EL BLOQUEO MAESTRO CORREGIDO
// Solo los clientes son expulsados. Admin y SubAdmin pueden pasar.
if ($_SESSION['user_rol'] == 'cliente') {
    header("Location: tienda.php");
    exit();
}
?>