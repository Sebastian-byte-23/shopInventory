<?php
include "db.php";

// Reiniciar AUTO_INCREMENT si las tablas están vacías
try {
    // Verificar si la tabla `venta` está vacía
    $check_empty_venta = $pdo->query("SELECT COUNT(*) AS count FROM venta");
    $row_venta = $check_empty_venta->fetch(PDO::FETCH_ASSOC);
    if ($row_venta['count'] == 0) {
        $pdo->exec("ALTER TABLE venta AUTO_INCREMENT = 1");
    }

    // Verificar si la tabla `venta_productos` está vacía
    $check_empty_venta_productos = $pdo->query("SELECT COUNT(*) AS count FROM venta_productos");
    $row_venta_productos = $check_empty_venta_productos->fetch(PDO::FETCH_ASSOC);
    if ($row_venta_productos['count'] == 0) {
        $pdo->exec("ALTER TABLE venta_productos AUTO_INCREMENT = 1");
    }

    // Consulta para mostrar las ventas y los productos asociados
    $sql_venta = "
        SELECT v.id_venta, p.nombre AS nombre_producto, vp.cantidad, vp.precio_unitario, v.total_venta, v.fecha_venta, e.nombre AS empleado_nombre
        FROM venta v
        JOIN venta_productos vp ON v.id_venta = vp.venta_id
        JOIN productos p ON vp.productos_id = p.id_productos
        JOIN empleado e ON v.id_empleado = e.id_empleado";
    $stmt = $pdo->prepare($sql_venta);
    $stmt->execute();
    $result_venta = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Consulta para calcular el total de ventas
    $sql_total_ventas = "SELECT SUM(total_venta) AS total_ventas FROM venta";
    $stmt_total_ventas = $pdo->prepare($sql_total_ventas);
    $stmt_total_ventas->execute();
    $total_ventas = $stmt_total_ventas->fetch(PDO::FETCH_ASSOC);
    $total_ventas = $total_ventas['total_ventas']; // Total de ventas

    // Resetear ventas
    if (isset($_POST['reset_ventas'])) {
        // Eliminar todas las ventas y productos asociados
        $pdo->exec("DELETE FROM venta_productos");
        $pdo->exec("DELETE FROM venta");

        // Reiniciar los contadores AUTO_INCREMENT
        $pdo->exec("ALTER TABLE venta AUTO_INCREMENT = 1");
        $pdo->exec("ALTER TABLE venta_productos AUTO_INCREMENT = 1");

        // Redirigir a la misma página para actualizar la vista
        header("Location: venta.php");
        exit();
    }
} catch (PDOException $e) {
    $error_message = "Error al cargar las ventas: " . htmlspecialchars($e->getMessage());
    $result_venta = []; // Garantizar que el resultado no cause errores en el HTML
}

// Exportar a CSV
if (isset($_POST['export_csv'])) {
    $filename = "ventas.csv";
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID Venta', 'Nombre Producto', 'Cantidad', 'Precio Unitario', 'Total Venta', 'Fecha Venta', 'Nombre Empleado']);

    $total_ventas = 0; // Inicializamos la variable para el total de ventas

    foreach ($result_venta as $row) {
        $precio_unitario = '₣ ' . number_format($row['precio_unitario'], 0, ',', '.');
        $total_venta = '₣ ' . number_format($row['total_venta'], 0, ',', '.');
        fputcsv($output, [
            $row['id_venta'],
            $row['nombre_producto'],
            $row['cantidad'],
            $precio_unitario,
            $total_venta,
            $row['fecha_venta'],
            $row['empleado_nombre']
        ]);
        $total_ventas += $row['total_venta']; // Sumamos el total de cada venta
    }

    // Añadir el total de ventas al final
    fputcsv($output, ['', '', '', '', 'Total Ventas', '₣ ' . number_format($total_ventas, 0, ',', '.')]);


    fclose($output);
    exit();
}

// Exportar a Excel
if (isset($_POST['export_excel'])) {
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment; filename="ventas.xls"');
    echo "<table border='1'>";
    echo "<thead><tr>
            <th>ID Venta</th>
            <th>Nombre Producto</th>
            <th>Cantidad</th>
            <th>Precio Unitario</th>
            <th>Total Venta</th>
            <th>Fecha Venta</th>
            <th>Nombre Empleado</th>
          </tr></thead>";
    echo "<tbody>";

    $total_ventas = 0; // Inicializamos la variable para el total de ventas

    foreach ($result_venta as $row) {
        echo "<tr>
                <td>" . htmlspecialchars($row["id_venta"]) . "</td>
                <td>" . htmlspecialchars($row["nombre_producto"]) . "</td>
                <td>" . htmlspecialchars($row["cantidad"]) . "</td>
                <td>₣ " . number_format($row["precio_unitario"], 0, ',', '.') . "</td>
                <td>₣ " . number_format($row["total_venta"], 0, ',', '.') . "</td>
                <td>" . htmlspecialchars($row["fecha_venta"]) . "</td>
                <td>" . htmlspecialchars($row["empleado_nombre"]) . "</td>
              </tr>";
        $total_ventas += $row["total_venta"]; // Sumamos el total de cada venta
    }

    echo "</tbody>";

    // Añadir el total de ventas al final
    echo "<tfoot><tr>
            <td colspan='4'><strong>Total Ventas:</strong></td>
            <td><strong>₣ " . number_format($total_ventas, 0, ',', '.') . "</strong></td>
            <td colspan='2'></td>
          </tr></tfoot>";

    echo "</table>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Vendedor</title>
    <link rel="stylesheet" href="css/nav1.css">
    <link rel="stylesheet" href="venta.css">

    <!-- Agregar el icono de FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

</head>

<body>
    <!-- Barra de navegación -->
    <nav>
        <ul class="nav-links">
            <li class="brand">Shop Inventory</li>
            <li><a href="menu_vende.php">Inicio</a></li>
            <li><a href="venta.php" class="active">Historial de Ventas</a></li>
            <li><a href="producto.php">Stock</a></li>
            <li class="logout"><a href="../index.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <div class="menu-opciones">
        <h2>Ventas</h2>

        <!-- Mostrar mensaje de error si ocurre algún problema -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <div class="form-container">
            <!-- Botones de exportación con íconos -->
            <form action="" method="post">
                <button type="submit" name="export_excel" class="export-btn excel">
                    <i class="fas fa-file-excel"></i>
                </button>
                <button type="submit" name="export_csv" class="export-btn csv">
                    <i class="fas fa-file-csv"></i>
                </button>
            </form>
            <form class="resetear" method="post" onsubmit="return confirm('¿Estás seguro de que deseas resetear todas las ventas?');">
                <button type="submit" name="reset_ventas" class="reset-btn">
                    <i class="fas fa-redo"></i> Resetear Ventas
                </button>
            </form>
        </div>

        <div class="opciones">
            <div class="opcion">
                <table border="1" class="ventas-table">
                    <thead>
                        <tr>
                            <th>ID Venta</th>
                            <th>Nombre Producto</th>
                            <th>Cantidad</th>
                            <th>Precio Unitario</th>
                            <th>Total Venta</th>
                            <th>Fecha Venta</th>
                            <th>Empleado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($result_venta)): ?>
                            <?php foreach ($result_venta as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row["id_venta"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["nombre_producto"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["cantidad"]); ?></td>
                                    <td><?php echo '$ ' . number_format($row["precio_unitario"], 0, ',', '.'); ?></td>
                                    <td><?php echo '$ ' . number_format($row["total_venta"], 0, ',', '.'); ?></td>
                                    <td><?php echo htmlspecialchars($row["fecha_venta"]); ?></td>
                                    <td><?php echo htmlspecialchars($row["empleado_nombre"]); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No hay ventas registradas</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <br>
                <h3>Total de Ventas: <?php echo '$ ' . number_format($total_ventas, 0, ',', '.'); ?></h3>
                <!-- Botón para resetear ventas -->

            </div>
        </div>
    </div>
</body>

</html>