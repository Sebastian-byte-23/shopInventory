<?php
include "db.php";

// Recoger los parámetros de búsqueda
$nombre = isset($_GET['nombre']) ? '%' . $_GET['nombre'] . '%' : '%';
$precio_min = isset($_GET['precio_min']) ? $_GET['precio_min'] : 0;
$precio_max = isset($_GET['precio_max']) ? $_GET['precio_max'] : PHP_INT_MAX;
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$proveedor = isset($_GET['proveedor']) ? $_GET['proveedor'] : '';
$ordenar_por = isset($_GET['ordenar_por']) ? $_GET['ordenar_por'] : '';

// Inicializar los parámetros de la consulta
$params = [
    $nombre,
    $precio_min,
    $precio_max
];

// Preparar la consulta base
$query = "SELECT p.*, c.categoria_padre_id, c.nombre AS categoria_nombre FROM productos p 
          LEFT JOIN categoria c ON p.categoria_id = c.id_categoria
          WHERE p.nombre LIKE ? 
          AND p.precio BETWEEN ? AND ?";

// Si hay filtros de categorías
if (!empty($categoria)) {
    $subcategorias_query = "SELECT id_categoria FROM categoria WHERE categoria_padre_id = ?";
    $subcategorias_stmt = $pdo->prepare($subcategorias_query);
    $subcategorias_stmt->execute([$categoria]);
    $subcategorias = $subcategorias_stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($subcategorias)) {
        // Crear placeholders posicionales para las subcategorías
        $placeholders = implode(',', array_fill(0, count($subcategorias), '?'));
        $query .= " AND (p.categoria_id = ? OR p.categoria_id IN ($placeholders))";

        // Agregar los valores de la categoría y las subcategorías al array de parámetros
        $params[] = $categoria;  // Agregar la categoría
        $params = array_merge($params, $subcategorias);  // Agregar las subcategorías
    } else {
        $query .= " AND p.categoria_id = ?";
        $params[] = $categoria;
    }
}

// Si hay filtro de proveedor
if (!empty($proveedor)) {
    // Comprobar si el proveedor tiene productos
    $productos_stmt = $pdo->prepare("SELECT php.productos_id FROM productos_has_proveedor php WHERE php.proveedor_id = ?");
    $productos_stmt->execute([$proveedor]);
    $productos_asociados = $productos_stmt->fetchAll(PDO::FETCH_COLUMN);

    // Si no hay productos asociados al proveedor, evitar que se ejecute la consulta de productos
    if (!empty($productos_asociados)) {
        // Si hay productos asociados, añadir la condición al query
        $placeholders = implode(',', array_fill(0, count($productos_asociados), '?'));
        $query .= " AND p.id_productos IN ($placeholders)";
        $params = array_merge($params, $productos_asociados);
    } else {
        // Si no hay productos asociados al proveedor, no hay resultados
        $no_results_message = "No se han encontrado resultados para este proveedor.";
    }
}

// Validar el parámetro de ordenación
$valid_order_columns = ['precio', 'categoria_nombre', 'nombre', 'id_productos'];  // Añadir id_productos a las columnas válidas
$valid_order_directions = ['ASC', 'DESC'];  // Direcciones válidas

// Si se pasa un parámetro de ordenación, validarlo
if (!empty($ordenar_por) && in_array($ordenar_por, $valid_order_columns)) {
    $order_direction = isset($_GET['ordenar_direction']) && in_array(strtoupper($_GET['ordenar_direction']), $valid_order_directions) ? strtoupper($_GET['ordenar_direction']) : 'ASC';

    // Si ordenar por precio, usar p.precio
    if ($ordenar_por == 'precio') {
        $query .= " ORDER BY p.precio " . $order_direction;
    } else {
        $query .= " ORDER BY " . $ordenar_por . " " . $order_direction;
    }
} else {
    // Si no es válido o no se ha especificado, usar el orden predeterminado (por id_productos ASC)
    $query .= " ORDER BY p.id_productos ASC";
}

// Ejecutar la consulta para obtener los productos
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener todas las categorías padre en un solo query
$categorias_padre = [];
$stmt_cat = $pdo->prepare("SELECT id_categoria, nombre FROM categoria");
$stmt_cat->execute();
$categorias_padre_data = $stmt_cat->fetchAll(PDO::FETCH_ASSOC);
foreach ($categorias_padre_data as $categoria) {
    $categorias_padre[$categoria['id_categoria']] = $categoria['nombre'];
}

// Comprobar si hay productos antes de realizar la consulta de proveedores
if (!empty($productos)) {
    $productos_ids = array_column($productos, 'id_productos');
    $placeholders = implode(',', array_fill(0, count($productos_ids), '?'));

    $proveedores_stmt = $pdo->prepare("SELECT php.productos_id, pr.nombre_proveedor 
                                        FROM productos_has_proveedor php
                                        JOIN proveedor pr ON pr.id_proveedor = php.proveedor_id
                                        WHERE php.productos_id IN ($placeholders)");
    $proveedores_stmt->execute($productos_ids);
    $proveedores_data = $proveedores_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organizar los proveedores por producto
    $proveedores_por_producto = [];
    foreach ($proveedores_data as $proveedor) {
        $proveedores_por_producto[$proveedor['productos_id']][] = $proveedor['nombre_proveedor'];
    }
} else {
    $proveedores_por_producto = [];
}


// Mostrar los productos en la tabla
if (empty($productos)) {
    // Si no hay productos (ya sea por proveedor o por búsqueda en general), mostrar el mensaje
    if (isset($no_results_message)) {
        echo "<tr><td colspan='10'>$no_results_message</td></tr>";
    } else {
        echo "<tr><td colspan='10'>No se han encontrado resultados.</td></tr>";
    }
} else {
    foreach ($productos as $producto) {
        // Asignar categoría y subcategoría
        $categoria_mostrada = isset($categorias_padre[$producto['categoria_padre_id']]) ? $categorias_padre[$producto['categoria_padre_id']] : (isset($producto['categoria_nombre']) ? $producto['categoria_nombre'] : 'N/A');
        $subcategoria_mostrada = $producto['categoria_padre_id'] ? $producto['categoria_nombre'] : '';  // Solo asignar subcategoría si hay un `categoria_padre_id`

        // Obtener los proveedores para este producto
        $proveedores_str = isset($proveedores_por_producto[$producto['id_productos']]) ? implode(', ', $proveedores_por_producto[$producto['id_productos']]) : '';


        // Mostrar cada producto en una fila de la tabla
        echo "<tr>";
        echo "<td>{$producto['id_productos']}</td>";
        echo "<td>" . htmlspecialchars($producto['nombre']) . "</td>";
        echo "<td>$ " . number_format($producto['precio'], 0, ',', '.') . "</td>";
        echo "<td>{$producto['stock']}</td>";
        echo "<td>{$producto['fecha_caducidad']}</td>";
        echo "<td>{$producto['codigo_barra']}</td>";
        echo "<td>" . htmlspecialchars($categoria_mostrada) . "</td>"; // Categoría principal
        echo "<td>" . htmlspecialchars($subcategoria_mostrada) . "</td>"; // Subcategoría
        echo "<td>" . htmlspecialchars($proveedores_str) . "</td>";
        echo "<td>
    <!-- Botón de Editar -->
    <button onclick=\"editProduct(" . htmlspecialchars(json_encode($producto)) . ")\" class='btn-edit'>
        <i class='fas fa-edit' title='Editar'></i>
    </button>
    
    <!-- Formulario de Eliminar -->
    <form method='POST' style='display:inline'>
        <input type='hidden' name='id_productos' value='{$producto['id_productos']}'>
        <button type='submit' name='delete' class='btn-delete'>
            <i class='fas fa-trash-alt' title='Eliminar'></i>
        </button>
    </form>
</td>";
        echo "</tr>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/index.css">

    <title>Document</title>
</head>

<body>

</body>

</html>