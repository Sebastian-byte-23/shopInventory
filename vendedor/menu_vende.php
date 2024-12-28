<?php
include "db.php"; // Conexión PDO

// Inicializar variables para mensajes
$mensaje = '';

// Establecer la zona horaria a la de Chile
date_default_timezone_set('America/Santiago');

// Obtener productos para el select
$productos_query = $pdo->query("SELECT id_productos, nombre, precio, stock FROM productos");
$result_productos = $productos_query->fetchAll(PDO::FETCH_ASSOC);

// Obtener IDs de empleados
$empleados_query = $pdo->query("SELECT id_empleado, rut FROM empleado");
$result_empleados = $empleados_query->fetchAll(PDO::FETCH_ASSOC);

// Verificar si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Tomar datos del formulario de manera segura
        $producto_ids = $_POST['producto_id'];
        $cantidades = $_POST['cantidad'];
        // Eliminar la variable fecha_venta del POST
        // $fecha_venta = $_POST['fecha_hora_venta'];  // Ya no es necesario
        $empleado_id = $_POST['empleado_id'];
        $total_venta = 0;

        // Usar la fecha y hora actuales de Chile
        $fecha_venta = date('Y-m-d H:i:s');  // Fecha y hora actual en Chile

        // Insertar la venta en la tabla `venta`
        $insert_venta = $pdo->prepare("INSERT INTO venta (total_venta, fecha_venta, id_empleado) VALUES (:total_venta, :fecha_venta, :empleado_id)");
        $insert_venta->bindParam(':total_venta', $total_venta, PDO::PARAM_INT); // Inicialmente 0, se actualizará después
        $insert_venta->bindParam(':fecha_venta', $fecha_venta, PDO::PARAM_STR);
        $insert_venta->bindParam(':empleado_id', $empleado_id, PDO::PARAM_INT);

        if ($insert_venta->execute()) {
            // Obtener el último ID de venta insertado
            $venta_id = $pdo->lastInsertId();

            // Procesar cada producto en la venta
            foreach ($producto_ids as $index => $producto_id) {
                $cantidad = $cantidades[$index];

                // Obtener el precio y stock del producto
                $precio_query = $pdo->prepare("SELECT precio, stock FROM productos WHERE id_productos = :producto_id");
                $precio_query->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
                $precio_query->execute();
                $row_precio = $precio_query->fetch(PDO::FETCH_ASSOC);

                if ($row_precio) {
                    $precio_unitario = $row_precio['precio'];
                    $stock_disponible = $row_precio['stock'];

                    // Verificar si hay suficiente stock
                    if ($stock_disponible >= $cantidad) {
                        $subtotal = $cantidad * $precio_unitario;
                        $total_venta += $subtotal;

                        // Insertar el detalle de la venta en la tabla `venta_productos`
                        $insert_detalle = $pdo->prepare("INSERT INTO venta_productos (venta_id, productos_id, cantidad, precio_unitario) VALUES (:venta_id, :producto_id, :cantidad, :precio_unitario)");
                        $insert_detalle->bindParam(':venta_id', $venta_id, PDO::PARAM_INT);
                        $insert_detalle->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
                        $insert_detalle->bindParam(':cantidad', $cantidad, PDO::PARAM_INT);
                        $insert_detalle->bindParam(':precio_unitario', $precio_unitario, PDO::PARAM_INT);
                        $insert_detalle->execute();

                        // Actualizar el stock
                        $nuevo_stock = $stock_disponible - $cantidad;
                        $update_stock = $pdo->prepare("UPDATE productos SET stock = :nuevo_stock WHERE id_productos = :producto_id");
                        $update_stock->bindParam(':nuevo_stock', $nuevo_stock, PDO::PARAM_INT);
                        $update_stock->bindParam(':producto_id', $producto_id, PDO::PARAM_INT);
                        $update_stock->execute();
                    } else {
                        $mensaje = "Error: No hay suficiente stock para el producto con ID $producto_id.";
                        break;
                    }
                } else {
                    $mensaje = "Error: El producto con ID $producto_id no existe.";
                    break;
                }
            }

            // Actualizar el total de la venta
            $update_venta = $pdo->prepare("UPDATE venta SET total_venta = :total_venta WHERE id_venta = :venta_id");
            $update_venta->bindParam(':total_venta', $total_venta, PDO::PARAM_INT);
            $update_venta->bindParam(':venta_id', $venta_id, PDO::PARAM_INT);
            $update_venta->execute();

            $mensaje = "Venta registrada y stock actualizado correctamente.";
        } else {
            $mensaje = "Error al insertar la venta.";
        }
    } catch (PDOException $e) {
        error_log("Error en el procesamiento de venta: " . $e->getMessage());
        $mensaje = "Ocurrió un error al procesar la venta. Inténtelo más tarde.";
    }
}

// Obtener IDs y nombres de empleados
$empleados_query = $pdo->query("SELECT id_empleado, rut, nombre FROM empleado");
$result_empleados = $empleados_query->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sellsman.css">
    <link rel="stylesheet" href="css/nav1.css">
    <title>Agregar Venta</title>
    <script>
        // Función para mostrar el snackbar
        function mostrarSnackbar() {
            var snackbar = document.getElementById("snackbar");
            snackbar.className = "show";
            setTimeout(function() {
                snackbar.className = snackbar.className.replace("show", "");
            }, 3000); // El snackbar se ocultará después de 3 segundos
        }

        // Mostrar el snackbar cuando la página cargue
        window.onload = mostrarSnackbar;

        function actualizarPrecio(select, index) {
            const precio = select.options[select.selectedIndex].getAttribute('data-precio');
            document.getElementsByName('precio_unitario[]')[index].value = precio;
        }

        function agregarProducto() {
            const productosDiv = document.getElementById('productos');
            const index = productosDiv.childElementCount;
            const productoHTML = ` 
                <div class="producto-item" id="producto-${index}">
                    <div class="field">
                        <label>Producto:</label>
                        <select name="producto_id[]" onchange="actualizarPrecio(this, ${index})" required>
                            <?php foreach ($result_productos as $row): ?>
                                <option value="<?php echo $row['id_productos']; ?>" data-precio="<?php echo $row['precio']; ?>">
                                    <?php echo htmlspecialchars($row['nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="field">
                        <label>Cantidad:</label>
                        <input type="number" name="cantidad[]" min="1" required>
                    </div>
                    <div class="field">
                        <label>Precio Unitario:</label>
                        <input type="number" name="precio_unitario[]" readonly>
                    </div>
                    <button type="button" onclick="eliminarProducto(${index})">Eliminar</button>
                </div>
            `;
            productosDiv.insertAdjacentHTML('beforeend', productoHTML);
        }

        function eliminarProducto(index) {
            const productoDiv = document.getElementById(`producto-${index}`);
            if (productoDiv) {
                productoDiv.remove();
            }
        }
    </script>
</head>

<body>
    <nav>
        <ul class="nav-links">
            <li class="brand">Shop Inventory</li>
            <li><a href="menu_vende.php" class="active">Inicio</a></li>
            <li><a href="venta.php">Historial de Ventas</a></li>
            <li><a href="producto.php">Stock</a></li>
            <li class="logout"><a href="../index.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <!-- Snackbar -->
    <div id="snackbar">Entraste como vendedor</div>

    <div class="formulario">
        <h3>Agregar nueva venta</h3>
        <br>
        <?php if ($mensaje): ?>
            <p><?php echo htmlspecialchars($mensaje); ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <!-- Campo para escanear el código de barras -->
            <div class="form-group">
                <label for="codigo_barra">Escanea el Producto:</label>
                <input class="btn-barras" type="text" id="codigo_barra" name="codigo_barra" maxlength="13" placeholder="Escanee el código de barras" autocomplete="off">
            </div>

            <div id="productos"></div>

            <!-- Eliminar el campo de fecha -->
            <!-- <div class="form-group">
                <label>Fecha y Hora de Venta:</label>
                <input type="datetime-local" name="fecha_hora_venta" required>
            </div> -->

            <div class="form-group">
                <label>Empleado:</label>
                <select class="btn-empleado" name="empleado_id" required>
                    <option class="btn-empleado" value="">Seleccione un empleado</option>
                    <?php foreach ($result_empleados as $row): ?>
                        <option value="<?php echo $row['id_empleado']; ?>">
                            <?php echo htmlspecialchars($row['nombre']); ?> <!-- Mostrar el nombre -->
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>


            <input type="submit" value="Agregar Venta" class="submit-button">
        </form>
    </div>
    <script src="js/menu_vende.js"></script>
</body>

</html>