<?php
// Incluir el archivo de conexión a la base de datos
include "db.php";

// Verificar si se ha recibido el parámetro 'id'
if (isset($_GET['id'])) {
    $empleadoId = $_GET['id'];

    // Preparar la consulta para eliminar al empleado
    $query = "DELETE FROM empleado WHERE id_empleado = :id_empleado";
    $stmt = $pdo->prepare($query);
    
    // Ejecutar la consulta
    try {
        $stmt->execute([
            ':id_empleado' => $empleadoId
        ]);
        
        echo "Empleado eliminado correctamente";
    } catch (PDOException $e) {
        echo "Error al eliminar el empleado: " . $e->getMessage();
    }
}
?>
