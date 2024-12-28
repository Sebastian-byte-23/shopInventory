<?php

session_start();
include 'db.php';

// Consulta para obtener empleados
$empleados = [];
try {
    $stmt = $pdo->query("SELECT id_empleado, nombre, tipo_empleado FROM empleado");
    $empleados = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener empleados: " . $e->getMessage();
}

// Inicializar variables para los mensajes de error y éxito
$error_usuario = '';
$error_contraseña = '';
$success_message = '';

// Comprobar si se ha enviado el formulario
if (isset($_POST['bt1'])) {
    // Tomar datos del formulario
    $user = trim($_POST['usuario']);
    $pass = $_POST['c1'];
    $passConfirm = $_POST['c2'];
    $tipo_empleado = $_POST['tipo_empleado'];
    $empleado_id = $_POST['empleado_id'];

    // Verificar que las contraseñas coincidan
    if ($pass !== $passConfirm) {
        $error_contraseña = "Las contraseñas no coinciden.";
    }

    // Si no hay errores, proceder con la inserción
    if (empty($error_contraseña)) {
        // Encriptar la contraseña
        $segurito = password_hash($pass, PASSWORD_DEFAULT);

        // Preparar la consulta para insertar en la tabla usuarios
        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (user, pass, tipo_empleado, empleado_id) VALUES (:user, :pass, :tipo_empleado, :empleado_id)");
            $stmt->bindParam(':user', $user);
            $stmt->bindParam(':pass', $segurito);
            $stmt->bindParam(':tipo_empleado', $tipo_empleado);
            $stmt->bindParam(':empleado_id', $empleado_id);

            // Ejecutar la consulta
            if ($stmt->execute()) {
                $success_message = "Usuario registrado exitosamente.";
            } else {
                $error_usuario = "Error al registrar el usuario.";
            }
        } catch (PDOException $e) {
            $error_usuario = "Error en la base de datos: " . $e->getMessage();
        }
    }

    // Almacenar mensajes en la sesión para mostrarlos en usuarios.php
    $_SESSION['error_usuario'] = $error_usuario;
    $_SESSION['error_contraseña'] = $error_contraseña;
    $_SESSION['success_message'] = $success_message;

    // Redirigir para evitar el reenvío del formulario
    header("Location: usuarios.php");
    exit();
}
