<?php
include "db.php";

// Verifica si la solicitud se ha hecho usando el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Asigna los valores recibidos a las variables locales
    $id = $_POST['id'];               // ID del empleado
    $usuario = $_POST['usuario'];     // Nombre de usuario del empleado
    $tipo_empleado = $_POST['tipo_empleado']; // Tipo de empleado (por ejemplo, 'administrador', 'empleado', etc.)
    $new_password = $_POST['new_password'];   // Nueva contraseña proporcionada por el usuario
    $confirm_password = $_POST['confirm_password']; // Confirmación de la nueva contraseña

    // Validar las contraseñas
    if ($new_password !== $confirm_password) {
        // Retornar error en formato JSON
        echo json_encode([
            'success' => false,
            'message' => 'Las contraseñas no coinciden.'
        ]);
        exit;
    }

    try {
        // Si hay nueva contraseña, actualizarla
        if (!empty($new_password)) {
            // Hashear la nueva contraseña
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            // Actualizar en la base de datos con la nueva contraseña
            $sql = "UPDATE usuarios SET user = ?, tipo_empleado = ?, pass = ? WHERE id_usuarios = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$usuario, $tipo_empleado, $hashed_password, $id]);

            // Retornar éxito en formato JSON
            echo json_encode([
                'success' => true,
                'message' => 'Usuario y contraseña actualizados con éxito.'
            ]);
        } else {
            // Si no hay nueva contraseña, solo actualizar el tipo de empleado
            $sql = "UPDATE usuarios SET user = ?, tipo_empleado = ? WHERE id_usuarios = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$usuario, $tipo_empleado, $id]);

            // Retornar éxito en formato JSON
            echo json_encode([
                'success' => true,
                'message' => 'Usuario y tipo de empleado actualizado con éxito.'
            ]);
        }
    } catch (Exception $e) {
        // En caso de error, retornar el mensaje de error
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar el usuario: ' . $e->getMessage()
        ]);
    }
} else {
    // Si no es un POST, retornar el mensaje de error
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido.'
    ]);
}
?>
