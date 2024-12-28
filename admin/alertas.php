<?php
include 'db.php';

try {
    // Consultar productos con bajo stock (menos de 10)
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE stock < :stock_min");
    $stmt->execute([':stock_min' => 10]);
    $productos_bajo_stock = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_message = "Error al obtener productos: " . htmlspecialchars($e->getMessage());
    $productos_bajo_stock = []; // Evitar errores en el HTML si la consulta falla
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="alertas.css"> <!-- Enlace a los estilos personalizados del snackbar y alertas -->
    <link rel="stylesheet" href="nav.css"> <!-- Estilos para la barra de navegación -->
    <link rel="stylesheet" href="css/snackbar.css"> <!-- Estilos para la barra de navegación -->

    <title>Alertas de Stock Bajo</title>
</head>

<body>

    <!-- Barra de Navegación -->
    <nav>
        <ul class="nav-links">
            <li class="brand">Shop Inventory</li> <!-- Nombre de la tienda o aplicación -->
            <li><a href="menu.php">Inicio</a></li>
            <li><a href="usuarios.php">Usuarios</a></li>
            <li><a href="gestion_local.php">Locales</a></li>
            <li><a href="index.php">Inventario</a></li>
            <li><a href="alertas.php" class="active">Alertas</a></li>
            <li><a href="categorias.php">Categorías</a></li>
            <li><a href="proveedores.php">Proveedores</a></li>
            <li class="logout"><a href="../index.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <!-- Contenido Principal -->
    <div class="container">
        <h1>Alertas de Productos en Bajo Stock</h1>
        <br>
        <div class="button-container">
            <button class="control-button" onclick="window.location.href='control_caducidad.php'">Ver Control de Caducidad</button>
            <button onclick="location.href='index.php'" class="button-i">Volver al Inventario</button>
        </div>

        <!-- Mostrar mensajes de error si ocurre algún problema -->
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Precio</th>
                    <th>Stock</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($productos_bajo_stock)): ?>
                    <tr>
                        <td colspan="4">No hay productos en bajo stock.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($productos_bajo_stock as $producto): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($producto['id_productos']); ?></td>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td><?php echo htmlspecialchars($producto['precio']); ?></td>
                            <td><?php echo htmlspecialchars($producto['stock']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Snackbar (mensaje de alerta de bajo stock) -->
        <div id="snackbar">¡Atención! Hay productos con bajo stock.</div>

        <!-- Script para mostrar el Snackbar cuando haya productos de bajo stock -->
        <script>
            // Mostrar el snackbar
            function showSnackbar() {
                var snackbar = document.getElementById("snackbar");
                snackbar.classList.add("show"); // Añadir la clase "show"
                snackbar.classList.remove("fadeOut"); // Asegurarse de que no se haya añadido fadeOut previamente

                setTimeout(function() {
                    snackbar.classList.add("fadeOut"); // Añadir fadeOut para asegurar que no reaparezca
                    snackbar.classList.remove("show"); // Eliminar la clase "show" después de la animación
                }, 3000); // Mantener el snackbar visible durante 3 segundos
            }

            var productosBajoStock = <?php echo json_encode(!empty($productos_bajo_stock)); ?>;
            if (productosBajoStock) {
                showSnackbar();
            }
        </script>

    </div>

</body>

</html>