<?php
include "db.php";

// Verificar que el ID del usuario esté presente
if (isset($_GET['id'])) {
    $id_usuario = $_GET['id'];

    // Eliminar usuario de la base de datos
    $sql_eliminar = "DELETE FROM usuarios WHERE id_usuarios = :id";
    $stmt = $pdo->prepare($sql_eliminar);
    $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Usuario eliminado con éxito.";
    } else {
        echo "Error al eliminar el usuario.";
    }
} else {
    echo "No se proporcionó un ID de usuario válido.";
}

$conn = null; // Cerrar la conexión
?>
