<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {
    $id_producto = $_POST['id_productos'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $fecha_caducidad = $_POST['fecha_caducidad'];
    $codigo_barra = $_POST['codigo_barra'];
    $usuario = $_POST['usuario']; 

    // Definir el tipo de movimiento según la acción
    $tipo_movimiento = isset($id_producto) ? 'actualización' : 'agregación'; // Si $id_producto existe, es actualización; de lo contrario, es una agregación

    // Actualizar o agregar el producto en la tabla productos
    $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, precio = ?, stock = ?, fecha_caducidad = ?, codigo_barra = ? WHERE id_productos = ?");
    $stmt->execute([$nombre, $precio, $stock, $fecha_caducidad, $codigo_barra, $id_producto]);

    // Registrar movimiento histórico
    $stmt = $pdo->prepare("INSERT INTO historial_movimientos (id_producto, fecha_movimiento, cantidad, tipo_movimiento, usuario) VALUES (?, NOW(), ?, ?, ?)");
    $stmt->execute([$id_producto, $stock, $tipo_movimiento, $usuario]);

    // Restablecer el valor del ID a 1 después de agregar o actualizar
    $pdo->exec("ALTER TABLE historial_movimientos AUTO_INCREMENT = 1");

    if ($stmt->rowCount() > 0) {
        echo "Producto actualizado y movimiento registrado correctamente.";
    } else {
        echo "Error al actualizar el producto o registrar el movimiento.";
    }
}

$stmt = $pdo->query("SELECT hm.*, p.nombre AS nombre_producto, p.precio AS precio_producto, p.stock AS stock_producto, p.fecha_caducidad, p.codigo_barra
                        FROM historial_movimientos hm
                        LEFT JOIN productos p ON hm.id_producto = p.id_productos");
$movimientos = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="nav.css"> <!-- Archivo de estilos para la navegación -->
    <link rel="stylesheet" href="css/historial.css"> <!-- Archivo de estilos para el historial -->
    <title>Historial de Movimientos</title>
</head>

<body>
    <nav>
        <ul class="nav-links">
            <li class="brand">Shop Inventory</li>
            <li><a href="menu.php">Inicio</a></li>
            <li><a href="usuarios.php">Usuarios</a></li>
            <li><a href="gestion_local.php">Locales</a></li>
            <li><a href="index.php">Inventario</a></li>
            <li><a href="alertas.php">Alertas</a></li>
            <li><a href="categorias.php">Categorías</a></li>
            <li><a href="proveedores.php">Proveedores</a></li>
            <li class="logout"><a href="../index.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Historial de Movimientos de Productos</h1>

        <a href="index.php" class="back-btn">Volver</a>
        <br><br>

        <table>
            <thead>
                <tr>
                    <th>Código de Barra</th>
                    <th>Producto</th>
                    <th>Tipo de Movimiento</th>
                    <th>Usuario</th>
                    <th>Precio</th>
                    <th>Fecha de cambio</th>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($movimientos as $movimiento): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($movimiento['codigo_barra']); ?></td>
                        <td><?php echo htmlspecialchars($movimiento['nombre_producto']); ?></td>
                        <td><?php echo htmlspecialchars($movimiento['tipo_movimiento']); ?></td>
                        <td><?php echo htmlspecialchars($movimiento['usuario']); ?></td>
                        <td><?php echo htmlspecialchars($movimiento['precio_producto']); ?></td>
                        <td><?php echo htmlspecialchars($movimiento['fecha_movimiento']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>