<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conectar a la base de datos
    include 'db.php';

    // Obtener los datos del formulario
    $empleadoId = $_POST['userId'];
    $nombre = $_POST['user'];
    $apellido = $_POST['apellido'];
    $tipoEmpleado = $_POST['tipo_empleado'];
    $direccion = $_POST['direccion_empleado'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    try {
        // Actualizar el empleado en la base de datos
        $sql = "UPDATE empleado SET nombre = :nombre, apellido = :apellido, tipo_empleado = :tipo_empleado, direccion_empleado = :direccion, telefono = :telefono, email = :email WHERE id_empleado = :id_empleado";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellido', $apellido);
        $stmt->bindParam(':tipo_empleado', $tipoEmpleado);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':id_empleado', $empleadoId, PDO::PARAM_INT);

        // Ejecutar la consulta
        if ($stmt->execute()) {
            echo "Empleado actualizado correctamente";
        } else {
            echo "Hubo un error al actualizar el empleado";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
