<?php
session_start();
require 'config/database.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['user_rol'] != 'admin' && $_SESSION['user_rol'] != 'subadmin')) {
    header("Location: dashboard.php");
    exit();
}

// --- LÓGICA PARA CREAR EMPLEADO ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if ($_SESSION['user_rol'] != 'admin') {
        header("Location: empleados.php?error=denied");
        exit();
    }

    $nombre = trim($_POST['nombre']);
    $dni = trim($_POST['dni']);
    $cargo = trim($_POST['cargo']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($nombre) || empty($dni) || empty($cargo) || empty($email) || empty($password)) {
        header("Location: empleados.php?error=1");
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
        $stmt->execute(['email' => $email]);
        if ($stmt->fetch()) {
            header("Location: empleados.php?error=2");
            exit();
        }

        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO empleados (nombre, dni, cargo, email) VALUES (:nombre, :dni, :cargo, :email)");
        $stmt->execute(['nombre' => $nombre, 'dni' => $dni, 'cargo' => $cargo, 'email' => $email]);

        $rol_sistema = 'empleado';
        if ($cargo === 'Administrador') $rol_sistema = 'admin';
        if ($cargo === 'SubAdministrador') $rol_sistema = 'subadmin';

        $stmt2 = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, :rol)");
        $stmt2->execute(['nombre' => $nombre, 'email' => $email, 'password' => $password_hash, 'rol' => $rol_sistema]);

        header("Location: empleados.php?success=1");
        exit();

    } catch (PDOException $e) {
        header("Location: empleados.php?error=3");
        exit();
    }
}

// --- LÓGICA PARA ELIMINAR EMPLEADO ---
if (isset($_GET['accion']) && $_GET['accion'] == 'eliminar' && isset($_GET['id'])) {
    
    if ($_SESSION['user_rol'] != 'admin') {
        header("Location: empleados.php?error=denied");
        exit();
    }

    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT email FROM empleados WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $emp = $stmt->fetch();

    if ($emp) {
        $pdo->prepare("DELETE FROM empleados WHERE id = :id")->execute(['id' => $id]);
        $pdo->prepare("DELETE FROM usuarios WHERE email = :email")->execute(['email' => $emp['email']]);
    }
    
    header("Location: empleados.php?success=2");
    exit();
}
?>