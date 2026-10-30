<?php
session_start();
require 'config/database.php';

// Solo el admin puede hacer esto
if (!isset($_SESSION['user_id']) || $_SESSION['user_rol'] != 'admin') {
    header("Location: dashboard.php");
    exit();
}

if (isset($_GET['accion']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $accion = $_GET['accion'];

    // 1. Evitar que el admin se banee o elimine a sí mismo
    if ($id == $_SESSION['user_id']) {
        header("Location: clientes.php?error=self");
        exit();
    }

    // 2. Consultar el rol del usuario que se quiere modificar
    $stmt = $pdo->prepare("SELECT rol FROM usuarios WHERE id = :id");
    $stmt->execute(['id' => $id]);
    $usuario_objetivo = $stmt->fetch();

    // 3. Si el usuario objetivo es admin, bloqueamos la acción
    if ($usuario_objetivo && $usuario_objetivo['rol'] == 'admin') {
        header("Location: clientes.php?error=admin");
        exit();
    }

    // 4. Si pasa las validaciones, ejecutamos la acción
    try {
        if ($accion == 'banear') {
            $stmt = $pdo->prepare("UPDATE usuarios SET estado = 'baneado' WHERE id = :id");
            $stmt->execute(['id' => $id]);
            header("Location: clientes.php?success=banned");
            
        } elseif ($accion == 'activar') {
            $stmt = $pdo->prepare("UPDATE usuarios SET estado = 'activo' WHERE id = :id");
            $stmt->execute(['id' => $id]);
            header("Location: clientes.php?success=activated");
            
        } elseif ($accion == 'eliminar') {
            $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->execute(['id' => $id]);
            header("Location: clientes.php?success=deleted");
        }
        exit();

    } catch (PDOException $e) {
        header("Location: clientes.php?error=db");
        exit();
    }
} else {
    header("Location: clientes.php");
    exit();
}
?>