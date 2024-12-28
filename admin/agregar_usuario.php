<?php
include "db.php"; // Incluir la conexión a la base de datos
include "gestion_local.php";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Obtener los datos del formulario
    $user = trim($_POST['user']);
    $tipo_empleado = trim($_POST['tipo_empleado']);
    $clave = trim($_POST['clave']);
    $empleado_id = intval($_POST['empleado_id']);

    // Encriptar la clave
    $hashed_password = password_hash($clave, PASSWORD_DEFAULT);

    try {
        // Insertar el usuario en la base de datos
        $stmt = $pdo->prepare("INSERT INTO usuarios (user, tipo_empleado, pass, empleado_id) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([$user, $tipo_empleado, $hashed_password, $empleado_id]);

        // Responder con mensaje de éxito
        echo "Usuario agregado correctamente.";
    } catch (PDOException $e) {
        // Responder con error si falla
        echo "Error al agregar usuario: " . $e->getMessage();
    }
}
