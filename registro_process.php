<?php
session_start();
require 'config/database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir y limpiar datos
    $nombre = trim($_POST['nombre']);
    $dni = trim($_POST['dni']);
    $telefono = trim($_POST['telefono']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validar campos vacíos
    if (empty($nombre) || empty($dni) || empty($telefono) || empty($email) || empty($password)) {
        header("Location: registro.php?error=1");
        exit();
    }

    // Verificar si el email ya existe
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    if ($stmt->fetch()) {
        header("Location: registro.php?error=2");
        exit();
    }

    // Encriptar la contraseña (¡Esto es clave para que el login funcione!)
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar en la tabla 'usuarios' (para el login)
    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, 'cliente')");
        $stmt->execute([
            'nombre' => $nombre,
            'email' => $email,
            'password' => $password_hash
        ]);
        
        $usuario_id = $pdo->lastInsertId();

        // Insertar también en la tabla 'clientes' (para la gestión de clientes)
        $stmt2 = $pdo->prepare("INSERT INTO clientes (nombre, dni_ruc, email, telefono) VALUES (:nombre, :dni, :email, :telefono)");
        $stmt2->execute([
            'nombre' => $nombre,
            'dni' => $dni,
            'email' => $email,
            'telefono' => $telefono
        ]);

        // Redirigir con mensaje de éxito
        header("Location: registro.php?success=1");
        exit();

    } catch (PDOException $e) {
        // Si hay un error en la base de datos
        header("Location: registro.php?error=3");
        exit();
    }
}
?>