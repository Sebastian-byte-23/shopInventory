<?php
include "db.php";

try {
    // Consulta para mostrar las transacciones
    $sql_transaccion = "SELECT id_transaccion, tipo_transaccion, fecha, venta_id_venta FROM transaccion";

    $stmt = $pdo->prepare($sql_transaccion);
    $stmt->execute();
    $result_transaccion = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error al cargar las transacciones: " . htmlspecialchars($e->getMessage());
    $result_transaccion = []; // Garantizar que el resultado no cause errores en el HTML
}

$sql_transaccion = "
    SELECT 
        t.id_transaccion, 
        t.tipo_transaccion, 
        t.fecha, 
        t.venta_id_venta, 
        p.id_proveedor
    FROM 
        transaccion t
    LEFT JOIN 
        productos_has_proveedor pp ON t.venta_id_venta = pp.venta_id_venta
    LEFT JOIN 
        proveedor p ON pp.proveedor_id = p.id_proveedor
";

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Vendedor</title>
    <link rel="stylesheet" href="css/nav1.css">
    <link rel="stylesheet" href="css/transaccion.css">
</head>

<body>
<script src="https://sdk.mercadopago.com/js/v2"></script>
    <!-- Barra de navegación -->
    <nav>
        <ul class="nav-links">
            <li class="brand">Shop Inventory</li> <!-- Texto al inicio -->
            <li><a href="menu_vende.php">Inicio</a></li>
            <li><a href="venta.php">Ventas</a></li>
            <li><a href="transaccion.php" class="active">Transacción</a></li>
            <li><a href="producto.php">Stock</a></li>
            <li class="logout"><a href="../index.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <div class="menu-opciones">
        <h2>Transacciones</h2>

        <!-- Mostrar mensaje de error si ocurre algún problema -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="opciones">
            <div class="opcion">
                <table border="1" class="transaccion-table">
                    <thead>
                        <tr>
                            <th>ID Transacción</th>
                            <th>Tipo de Transacción</th>
                            <th>Fecha</th>
                            <th>ID Venta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($result_transaccion)): ?>
                            <?php foreach ($result_transaccion as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row["id_transaccion"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["tipo_transaccion"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["fecha"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["venta_id_venta"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["id_proveedor"]); ?></td>

                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan='5'>No hay transacciones disponibles</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>

<?php
$conn = null; // Cerrar la conexión PDO
?>