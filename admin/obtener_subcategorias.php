<?php
include "db.php";

// Obtener el id de la categoría principal (padre) desde la solicitud
$categoria_padre_id = isset($_GET['categoria_padre_id']) ? $_GET['categoria_padre_id'] : null;

if ($categoria_padre_id) {
    // Obtener las subcategorías de la categoría seleccionada
    $stmt = $pdo->prepare("SELECT * FROM categoria WHERE categoria_padre_id = ?");
    $stmt->execute([$categoria_padre_id]);
    $subcategorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver las subcategorías como un array JSON
    echo json_encode($subcategorias);
}
?>
