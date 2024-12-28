<?php
include "db.php";

try {
    // Consulta para mostrar los productos
    $sql_productos = "SELECT id_productos, nombre, precio, stock, codigo_barra FROM productos";
    $stmt = $pdo->prepare($sql_productos);
    $stmt->execute();
    $result_productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error al cargar los productos: " . htmlspecialchars($e->getMessage());
    $result_productos = []; // Garantizar que el resultado no cause errores en el HTML
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Vendedor</title>
    <link rel="stylesheet" href="css/nav1.css">
    <link rel="stylesheet" href="producto.css">
</head>

<body>
    <!-- Barra de navegación -->
    <nav>
        <ul class="nav-links">
            <li class="brand">Shop Inventory</li> <!-- Texto al inicio -->
            <li><a href="menu_vende.php">Inicio</a></li>
            <li><a href="venta.php">Historial de Ventas</a></li>
            <li><a href="producto.php" class="active">Stock</a></li>
            <li class="logout"><a href="../index.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <div class="menu-opciones">
        <h2>Productos</h2>
<br>
        <!-- Mostrar mensaje de error si ocurre algún problema -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="opciones">
            <div class="opcion">
                <table border="1" class="productos-table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Nombre</th>
                            <th>Precio</th>
                            <th>Stock</th>
                            <th>Código de Barra</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($result_productos)): ?>
                            <?php foreach ($result_productos as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row["id_productos"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["nombre"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["precio"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["stock"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["codigo_barra"]); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan='4'>No hay productos disponibles</td>
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