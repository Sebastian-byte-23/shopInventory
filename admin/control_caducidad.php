<?php

require_once 'db.php';

// Eliminar productos duplicados antes de crear el índice
$pdo->exec("
    DELETE t1 FROM producto_control t1
    INNER JOIN producto_control t2 
    WHERE t1.id_control > t2.id_control AND t1.id_productos = t2.id_productos
");

// Comprobar si el índice existe antes de crearlo
$result = $pdo->query("SHOW INDEXES FROM producto_control WHERE Key_name = 'idx_id_productos'")->fetch();
if (!$result) {
    $pdo->exec("CREATE UNIQUE INDEX idx_id_productos ON producto_control(id_productos)");
}

// Crear la tabla 'producto_control' si no existe
$pdo->exec("
    CREATE TABLE IF NOT EXISTS `producto_control` (
        `id_control` INT AUTO_INCREMENT PRIMARY KEY,
        `id_productos` INT NOT NULL,
        `nombre` VARCHAR(200) NOT NULL,
        `precio` INT NOT NULL,
        `stock` INT NOT NULL,
        `fecha_caducidad` DATETIME NOT NULL,
        `estado` ENUM('activo', 'caducado', 'merma') DEFAULT 'activo',
        `fecha_registro` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`id_productos`) REFERENCES `productos`(`id_productos`)
    )
");

// Función para actualizar el control de caducidad
function actualizarControlCaducidad($pdo)
{
    // Seleccionar productos con fecha de caducidad en menos de 7 días o caducados
    $productos = $pdo->query("
        SELECT id_productos, nombre, precio, stock, fecha_caducidad
        FROM productos
        WHERE fecha_caducidad < DATE_ADD(NOW(), INTERVAL 7 DAY)
    ")->fetchAll();

    foreach ($productos as $producto) {
        // Calcular la diferencia de días desde la caducidad
        $fecha_caducidad = new DateTime($producto['fecha_caducidad']);
        $fecha_actual = new DateTime();
        $intervalo = $fecha_actual->diff($fecha_caducidad);
        $dias_restantes = $intervalo->format('%a'); // Días transcurridos desde la caducidad

        // Determinar el estado del producto
        if ($dias_restantes >= 30) {
            $estado = 'merma'; // Cambiar a "merma" si han pasado más de 30 días
        } elseif ($producto['fecha_caducidad'] < date('Y-m-d H:i:s')) {
            $estado = 'caducado'; // Cambiar a "caducado" si ya ha pasado la fecha
        } else {
            $estado = 'activo'; // Producto activo si aún no ha caducado
        }

        // Insertar o actualizar el producto en 'producto_control'
        $stmt = $pdo->prepare("
            INSERT INTO producto_control (id_productos, nombre, precio, stock, fecha_caducidad, estado)
            VALUES (:id_productos, :nombre, :precio, :stock, :fecha_caducidad, :estado)
            ON DUPLICATE KEY UPDATE estado = VALUES(estado), fecha_caducidad = VALUES(fecha_caducidad)
        ");
        $stmt->execute([
            ':id_productos' => $producto['id_productos'],
            ':nombre' => $producto['nombre'],
            ':precio' => $producto['precio'],
            ':stock' => $producto['stock'],
            ':fecha_caducidad' => $producto['fecha_caducidad'],
            ':estado' => $estado
        ]);
    }
}

// Actualización de la fecha de caducidad cuando se edita un producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_productos'])) {
    // Obtener los datos del formulario
    $id_productos = $_POST['id_productos'];
    $nueva_fecha_caducidad = $_POST['fecha_caducidad'];

    // Actualizar la fecha de caducidad del producto
    $stmt = $pdo->prepare("UPDATE productos SET fecha_caducidad = :fecha_caducidad WHERE id_productos = :id_productos");
    $stmt->execute([
        ':fecha_caducidad' => $nueva_fecha_caducidad,
        ':id_productos' => $id_productos
    ]);

    // Ahora actualizamos la tabla 'producto_control'
    $fecha_caducidad = new DateTime($nueva_fecha_caducidad);
    $fecha_actual = new DateTime();
    $intervalo = $fecha_actual->diff($fecha_caducidad);
    $dias_restantes = $intervalo->format('%a'); // Días restantes desde la fecha de caducidad

    // Determinar el estado del producto
    if ($dias_restantes >= 30) {
        $estado = 'merma'; // Cambiar a "merma" si han pasado más de 30 días
    } elseif ($fecha_caducidad < $fecha_actual) {
        $estado = 'caducado'; // Cambiar a "caducado" si ya ha pasado la fecha
    } else {
        $estado = 'activo'; // Producto activo si aún no ha caducado
    }

    // Insertar o actualizar en 'producto_control'
    $stmt = $pdo->prepare("
        INSERT INTO producto_control (id_productos, nombre, precio, stock, fecha_caducidad, estado)
        VALUES (:id_productos, :nombre, :precio, :stock, :fecha_caducidad, :estado)
        ON DUPLICATE KEY UPDATE 
            fecha_caducidad = VALUES(fecha_caducidad),
            estado = VALUES(estado)
    ");
    $stmt->execute([
        ':id_productos' => $id_productos,
        ':nombre' => $_POST['nombre'],  // Asegúrate de que estos valores se envíen correctamente
        ':precio' => $_POST['precio'],
        ':stock' => $_POST['stock'],
        ':fecha_caducidad' => $nueva_fecha_caducidad,
        ':estado' => $estado
    ]);
}

// Llama a la función al cargar la página para actualizar el control de mermas
actualizarControlCaducidad($pdo);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="css/control.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <title>Control de Mermas y Caducidad</title>
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
        <h2>Control de Mermas y Caducidad</h2>
        <button onclick="location.href='alertas.php'" id="button-cadu">Volver</button>
        <br><br>
        <div id="alertas">
            <?php
            // Obtener productos caducados o próximos a caducar en una semana
            $alertas = $pdo->query("
                SELECT * FROM producto_control 
                WHERE estado = 'caducado' 
                OR fecha_caducidad < DATE_ADD(NOW(), INTERVAL 7 DAY)
                OR estado = 'merma'
            ")->fetchAll();

            if (empty($alertas)) {
                echo "<p>No hay productos caducados o próximos a caducar.</p>";
            } else {
                foreach ($alertas as $alerta) {
                    // Determinar la clase de alerta y el progreso según el estado
                    $clase_alerta = ($alerta['estado'] === 'caducado') ? "caducado" : (($alerta['estado'] === 'merma') ? "merma" : "proximo");
                    $fecha_caducidad = new DateTime($alerta['fecha_caducidad']);
                    $fecha_actual = new DateTime();
                    $intervalo = $fecha_actual->diff($fecha_caducidad);
                    $dias_restantes = $intervalo->format('%a'); // Obtener los días restantes

                    echo "<div class='alert {$clase_alerta}'>";

                    // Iconos representativos
                    if ($alerta['estado'] === 'caducado') {
                        echo "<i class='fas fa-times-circle' title='Producto caducado'></i>";
                    } elseif ($alerta['estado'] === 'merma') {
                        echo "<i class='fas fa-triangle-exclamation' title='Producto en merma'></i>";
                    } else {
                        echo "<i class='fas fa-exclamation-circle' title='Producto próximo a caducar'></i>";
                    }

                    // Barra de progreso (dependiendo de los días restantes)
                    $progreso = ($dias_restantes <= 0) ? 0 : ($dias_restantes / 7) * 100; // 100% de progreso en 7 días
                    $progreso = round($progreso, 1);  // Redondear el porcentaje a un decimal

                    // Clases para la barra de progreso según el estado
                    $clase_barra = ($alerta['estado'] === 'caducado') ? "danger" : (($alerta['estado'] === 'merma') ? "secondary" : (($dias_restantes <= 2) ? "warning" : "success"));

                    echo "<div class='progress-bar {$clase_barra}'>
                            <span style='width: {$progreso}%;'></span>
                          </div>";

                    // Mostrar porcentaje con un solo decimal
                    echo "<p>Progreso: <strong>{$progreso}%</strong></p>";

                    // Mostrar detalles del producto
                    echo "Producto: <strong>{$alerta['nombre']}</strong><br>";
                    echo "Estado: <strong>{$alerta['estado']}</strong><br>";
                    echo "Fecha de caducidad: <strong>{$alerta['fecha_caducidad']}</strong><br>";

                    echo "</div>"; // Cerrar div .alert
                }
            }
            ?>
        </div>
    </div>
</body>

</html>
