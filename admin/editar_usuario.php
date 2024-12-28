<?php
// Iniciar sesión y conexión a la base de datos
include "db.php";

// Verificar si los datos han sido enviados
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idUsuario = $_POST['id_usuarios'];
    $usuario = $_POST['user'];
    $tipoEmpleado = $_POST['tipo_empleado'];
    $clave = $_POST['clave'];

    // Si la clave no está vacía, actualizarla, si no, mantener la clave anterior
    if (!empty($clave)) {
        $clave = password_hash($clave, PASSWORD_DEFAULT); // Encriptar la clave
        $stmt = $pdo->prepare("UPDATE usuarios SET user = ?, tipo_empleado = ?, pass = ? WHERE id_usuarios = ?");
        $stmt->execute([$usuario, $tipoEmpleado, $clave, $idUsuario]);
    } else {
        $stmt = $pdo->prepare("UPDATE usuarios SET user = ?, tipo_empleado = ? WHERE id_usuarios = ?");
        $stmt->execute([$usuario, $tipoEmpleado, $idUsuario]);
    }

    // Redirigir de nuevo a la página de usuarios o mostrar un mensaje de éxito
    echo "Usuario actualizado correctamente";
    header("Location: usuarios.php"); // O puedes redirigir a la página de detalles del usuario
    exit;
}
?>

