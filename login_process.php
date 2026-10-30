<?php
session_start();
require 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validar que no estén vacíos
    if (empty($email) || empty($password)) {
        header("Location: index.php?error=1");
        exit();
    }

    // Buscar al usuario en la base de datos
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // Verificar si el usuario existe y si la contraseña es correcta
    if ($user && password_verify($password, $user['password'])) {
        
        // Verificar si está baneado
        if (isset($user['estado']) && $user['estado'] == 'baneado') {
            header("Location: index.php?error=3");
            exit();
        }

        // Guardar los datos en la sesión
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_nombre'] = $user['nombre'];
        $_SESSION['user_rol'] = $user['rol'];
        $_SESSION['user_email'] = $user['email']; // <-- ESTA LÍNEA ES LA NUEVA

        // Redirigir según el rol
        if ($user['rol'] == 'admin' || $user['rol'] == 'subadmin' || $user['rol'] == 'empleado') {
            header("Location: dashboard.php");
        } else {
            header("Location: tienda.php");
        }
        exit();

    } else {
        // Usuario o contraseña incorrectos
        header("Location: index.php?error=2");
        exit();
    }
}
?>