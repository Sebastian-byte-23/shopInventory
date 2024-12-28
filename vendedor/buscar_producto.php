<?php
include "db.php"; // Conexión PDO

// Comprobar si se ha enviado el código de barras
if (isset($_GET['codigo_barra'])) {
    $codigo_barra = $_GET['codigo_barra'];

    // Buscar el producto por código de barras
    $query = $pdo->prepare("SELECT id_productos, nombre, precio FROM productos WHERE codigo_barra = :codigo_barra");
    $query->bindParam(':codigo_barra', $codigo_barra);
    $query->execute();

    $producto = $query->fetch(PDO::FETCH_ASSOC);

    if ($producto) {
        echo json_encode($producto); // Devolver los datos del producto como JSON
    } else {
        echo json_encode(['error' => 'Producto no encontrado']);
    }
}
