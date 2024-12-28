<?php
include 'db.php';
include "producto.php";
include "exportar.php";

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
$usuario_actual = isset($_SESSION['usuario']) ? $_SESSION['usuario'] : 'Administrador';

$tipo_movimiento = '';
$cantidad = 0;
$message = '';

// Manejo de solicitudes POST (Agregar, Editar, Eliminar)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add'])) {
        $message = agregarProducto($_POST['nombre'], $_POST['precio'], $_POST['stock'], $_POST['fecha_caducidad'], $_POST['codigo_barra'], $_POST['categoria_id'], $_POST['proveedor_ids'], $usuario_actual);
    } elseif (isset($_POST['edit'])) {
        $message = actualizarProducto($_POST['id_productos'], $_POST['nombre'], $_POST['precio'], $_POST['stock'], $_POST['fecha_caducidad'], $_POST['codigo_barra'], $_POST['categoria_id'], $_POST['proveedor_ids'], $usuario_actual);
    } elseif (isset($_POST['delete'])) {
        $message = eliminarProducto($_POST['id_productos']);
    }
}

// Configurar autoincremento desde 1 en la tabla `categoria`
$pdo->exec("ALTER TABLE categoria AUTO_INCREMENT = 1");

// Recoger los parámetros de búsqueda de la solicitud GET
$nombre = isset($_GET['nombre']) ? '%' . $_GET['nombre'] . '%' : '%';
$precio_min = isset($_GET['precio_min']) ? $_GET['precio_min'] : 0;
$precio_max = isset($_GET['precio_max']) ? $_GET['precio_max'] : PHP_INT_MAX;
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
$proveedor = isset($_GET['proveedor']) ? $_GET['proveedor'] : '';
$ordenar_por = isset($_GET['ordenar_por']) ? $_GET['ordenar_por'] : '';

// Construir la consulta SQL base
$query = "SELECT p.* FROM productos p 
          LEFT JOIN categoria c ON p.categoria_id = c.id_categoria
          WHERE p.nombre LIKE :nombre 
          AND p.precio BETWEEN :precio_min AND :precio_max";

// Inicializar array de parámetros
$params = [
    ':nombre' => $nombre,
    ':precio_min' => $precio_min,
    ':precio_max' => $precio_max
];

// Agregar filtro de categoría si se ha seleccionado una
if (!empty($categoria)) {
    // Obtener todas las subcategorías de la categoría seleccionada
    $subcategorias_query = "SELECT id_categoria FROM categoria WHERE categoria_padre_id = :categoria";
    $subcategorias_stmt = $pdo->prepare($subcategorias_query);
    $subcategorias_stmt->bindParam(':categoria', $categoria);
    $subcategorias_stmt->execute();
    $subcategorias = $subcategorias_stmt->fetchAll(PDO::FETCH_COLUMN);

    // Preparar la consulta principal
    if (!empty($subcategorias)) {
        // Si hay subcategorías, agregamos el filtro para ellas
        $placeholders = implode(',', array_fill(0, count($subcategorias) + 1, '?')); // Agregar un ? extra para la categoría seleccionada
        $query .= " AND (p.categoria_id = ? OR p.categoria_id IN ($placeholders))";
        $params = array_merge([$categoria], $subcategorias); // Primero agregar el id de la categoría principal
    } else {
        // Solo filtrar por la categoría seleccionada
        $query .= " AND p.categoria_id = ?";
        $params[] = $categoria; // Agregar solo la categoría seleccionada
    }
}


// Agregar filtro de proveedor si se ha seleccionado uno
if (!empty($proveedor)) {
    $query .= " AND EXISTS (
                    SELECT 1 FROM productos_has_proveedor php 
                    WHERE php.productos_id = p.id_productos 
                    AND php.proveedor_id = :proveedor
                )";
    $params[':proveedor'] = $proveedor;
}



// Preparar la consulta
$stmt = $pdo->prepare($query);
$stmt->execute($params);

// Obtener los productos filtrados
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener solo categorías principales para el filtro (sin subcategorías)
$categoria_query = "SELECT * FROM categoria WHERE categoria_padre_id IS NULL";
$categoria_stmt = $pdo->prepare($categoria_query);
$categoria_stmt->execute();
$categorias = $categoria_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="css/filtros.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Incluir jQuery desde un CDN -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Asegúrate de incluir select2 después de jQuery -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">


    <!-- Tu archivo de scripts -->

    <title>Inventario</title>
</head>

<body>
    <nav>
        <ul class="nav-links">
            <li class="brand">Shop Inventory</li>
            <li><a href="menu.php">Inicio</a></li>
            <li><a href="usuarios.php">Usuarios</a></li>
            <li><a href="gestion_local.php">Locales</a></li>
            <li><a href="index.php" class="active">Inventario</a></li>
            <li><a href="alertas.php">Alertas</a></li>
            <li><a href="categorias.php">Categorías</a></li>
            <li><a href="proveedores.php">Proveedores</a></li>
            <li class="logout"><a href="../index.php">Cerrar Sesión</a></li>
        </ul>
    </nav>
    <div class="container1">
        <h1>Gestión de Inventario</h1>
        <!-- Mensaje de alerta si existe -->
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>
        <br><br><br>

        <div class="action-buttons-container">
            <button class="add-button" onclick="openAddModal()">Agregar Producto</button>
            <button id="toggleFiltersBtn">Mostrar Búsqueda Avanzada</button>
            <button onclick="location.href='historial_movimientos.php'" class="button-ver">Ver movimientos</button>
            <button onclick="location.href='proveedores.php'" class="button-ver">Ver Proveedores</button>
            <button onclick="location.href='categorias.php'" class="button-categorias">Ver Categorías</button>
            <button onclick="location.href='alertas.php'" class="button-alertas">Ver Alertas</button>
        </div>

        <div class="export-buttons-container">
            <form method="post" action="javascript:void(0);">
                <button type="button" onclick="exportToExcel()" class="export-btn excel">
                    <i class="fas fa-file-excel"></i>
                </button>
                <button type="button" onclick="exportToCSV()" class="export-btn csv">
                    <i class="fas fa-file-csv"></i>
                </button>
            </form>
        </div>

        <!-- Modal para agregar o editar producto -->
        <div id="modal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2 id="modal-title">Agregar Nuevo Producto</h2>
                <form method="POST" id="modal-form" class="product-form" onsubmit="cleanPriceBeforeSubmit()">
                    <input type="hidden" id="id_productos" name="id_productos">
                    <label>Nombre:</label>
                    <input type="text" name="nombre" required>
                    <label>Precio:</label>
                    <input type="text" id="precio" name="precio" required oninput="formatPrice(this)">
                    <label>Stock:</label>
                    <input type="number" name="stock" required>
                    <label>Fecha de Caducidad:</label>
                    <input type="datetime-local" name="fecha_caducidad" required>
                    <label>Código de Barra:</label>
                    <input type="text" id="codigo_barra" name="codigo_barra" required>
                    <label>Seleccionar Categoría:</label>
                    <select name="categoria_padre_id" id="categoria_padre" required onchange="cargarSubcategorias()">
                        <option value="">Seleccionar Categoría</option>
                        <?php
                        // Obtener categorías principales (padres)
                        $categorias_padre = $pdo->query("SELECT * FROM categoria WHERE categoria_padre_id IS NULL")->fetchAll();
                        foreach ($categorias_padre as $categoria_padre) {
                            // Verificar si la categoría es la seleccionada
                            $selected = ($categoria_padre['id_categoria'] == $categoria_seleccionada) ? 'selected' : '';
                            echo "<option value='{$categoria_padre['id_categoria']}' $selected>{$categoria_padre['nombre']}</option>";
                        }
                        ?>
                    </select>

                    <label>Seleccionar Subcategoria:</label>
                    <select name="categoria_id" id="categoria_id" required>
                        <option value="">Seleccionar Subcategoria</option>
                        <?php
                        // Obtener las subcategorías de la categoría seleccionada
                        if (!empty($categoria_seleccionada)) {
                            $subcategorias = $pdo->prepare("SELECT * FROM categoria WHERE categoria_padre_id = ?");
                            $subcategorias->execute([$categoria_seleccionada]);
                            $subcategorias = $subcategorias->fetchAll();

                            foreach ($subcategorias as $subcategoria) {
                                // Verificar si la subcategoría es la seleccionada
                                $selected = ($subcategoria['id_categoria'] == $subcategoria_seleccionada) ? 'selected' : '';
                                echo "<option value='{$subcategoria['id_categoria']}' $selected>{$subcategoria['nombre']}</option>";
                            }
                        }
                        ?>
                    </select>


                    <label>Seleccionar Proveedores:</label>
                    <select name="proveedor_ids[]" id="proveedores" multiple="multiple" required>
                        <?php
                        $proveedores = $pdo->query("SELECT * FROM proveedor")->fetchAll();
                        foreach ($proveedores as $proveedor) {
                            echo "<option value='{$proveedor['id_proveedor']}'>{$proveedor['nombre_proveedor']}</option>";
                        }
                        ?>
                    </select>
                    <input type="hidden" name="usuario" value="<?php echo htmlspecialchars($usuario_actual); ?>">
                    <br>
                    <button class="add-btn"type="submit" id="add-button" name="add">Agregar Producto</button>
                    <br><br>
                    <button type="submit" id="edit-button" name="edit" style="display:none;">Actualizar Producto</button>
                </form>
            </div>
        </div>

        <script>
            function cargarSubcategorias() {
                // Obtener el id de la categoría seleccionada
                var categoriaPadreId = document.getElementById('categoria_padre').value;

                // Verificar que se haya seleccionado una categoría
                if (categoriaPadreId) {
                    // Realizar la solicitud AJAX
                    var xhr = new XMLHttpRequest();
                    xhr.open("GET", "obtener_subcategorias.php?categoria_padre_id=" + categoriaPadreId, true);
                    xhr.onload = function() {
                        if (xhr.status == 200) {
                            // Limpiar el campo de subcategorías
                            var subcategoriaSelect = document.getElementById('categoria_id');
                            subcategoriaSelect.innerHTML = '<option value="">Seleccionar Subcategoria</option>'; // Resetea las opciones

                            // Obtener las subcategorías del servidor
                            var subcategorias = JSON.parse(xhr.responseText);

                            // Añadir las subcategorías al select
                            subcategorias.forEach(function(subcategoria) {
                                var option = document.createElement('option');
                                option.value = subcategoria.id_categoria;
                                option.text = subcategoria.nombre;
                                subcategoriaSelect.appendChild(option);
                            });
                        }
                    };
                    xhr.send();
                } else {
                    // Si no hay categoría seleccionada, resetear las subcategorías
                    document.getElementById('categoria_id').innerHTML = '<option value="">Seleccionar Subcategoria</option>';
                }
            }
        </script>

        <div class="filters-container" id="filtersContainer" style="display: none;">
            <label class="busqueda">Búsqueda Avanzada</label>
            <br><br>
            <form id="filterForm">
                <!-- Campo de Búsqueda por Nombre -->
                <div class="filter-group">
                    <label for="nombre">Buscar por nombre:</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Buscar por nombre" oninput="applyFilters()" value="<?= isset($_GET['nombre']) ? $_GET['nombre'] : '' ?>">
                </div>

                <!-- Filtro de Categoría -->
                <div class="filter-group">
                    <label for="categoria">Seleccionar categoría:</label>
                    <select name="categoria" id="categoria" onchange="applyFilters()">
                        <option value="">Seleccionar categoría</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id_categoria']; ?>" <?= (isset($_GET['categoria']) && $_GET['categoria'] == $cat['id_categoria']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($cat['nombre']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Filtro de Ordenación -->
                <div class="filter-group">
                    <label for="ordenar_por">Ordenar por:</label>
                    <select name="ordenar_por" id="ordenar_por" onchange="applyFilters()">
                        <option value="">Ordenar por</option>
                        <option value="precio" <?= (isset($_GET['ordenar_por']) && $_GET['ordenar_por'] == 'precio') ? 'selected' : ''; ?>>Precio</option>
                        <option value="categoria_nombre" <?= (isset($_GET['ordenar_por']) && $_GET['ordenar_por'] == 'categoria_nombre') ? 'selected' : ''; ?>>Categoría</option>
                        <option value="nombre" <?= (isset($_GET['ordenar_por']) && $_GET['ordenar_por'] == 'nombre') ? 'selected' : ''; ?>>Nombre</option>
                    </select>
                </div>

                <!-- Filtro de Dirección de Ordenación -->
                <div class="filter-group">
                    <label for="ordenar_direction">Dirección de Ordenación:</label>
                    <select name="ordenar_direction" id="ordenar_direction" onchange="applyFilters()">
                        <option value="">Seleccionar dirección</option>
                        <option value="ASC" <?= (isset($_GET['ordenar_direction']) && $_GET['ordenar_direction'] == 'ASC') ? 'selected' : ''; ?>>Ascendente</option>
                        <option value="DESC" <?= (isset($_GET['ordenar_direction']) && $_GET['ordenar_direction'] == 'DESC') ? 'selected' : ''; ?>>Descendente</option>
                    </select>
                </div>


                <!-- Filtro de Proveedor -->
                <div class="filter-group">
                    <label for="proveedor">Seleccionar proveedor:</label>
                    <select name="proveedor" id="proveedor" onchange="applyFilters()">
                        <option value="">Seleccionar proveedor</option>
                        <?php foreach ($proveedores as $proveedor): ?>
                            <option value="<?= $proveedor['id_proveedor']; ?>" <?= (isset($_GET['proveedor']) && $_GET['proveedor'] == $proveedor['id_proveedor']) ? 'selected' : ''; ?>>
                                <?= htmlspecialchars($proveedor['nombre_proveedor']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>


        <script>
            // Mostrar/ocultar el formulario al hacer clic en el botón
            document.getElementById('toggleFiltersBtn').addEventListener('click', function() {
                const filtersContainer = document.getElementById('filtersContainer');
                if (filtersContainer.style.display === 'none') {
                    filtersContainer.style.display = 'block';
                    this.innerText = 'Ocultar Búsqueda Avanzada';
                } else {
                    filtersContainer.style.display = 'none';
                    this.innerText = 'Mostrar Búsqueda Avanzada';
                }
            });
        </script>

        <div class="product-table" id="producTable">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Fecha de Caducidad</th>
                        <th>Código de Barra</th>
                        <th>Categoría</th>
                        <th>Subcategoría</th>
                        <th>Proveedor(es)</th>
                        <th>Acciones</th>
                        <!-- Más encabezados según sea necesario -->
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    <?php include 'filtros.php'; // Incluye solo las filas de productos 
                    ?>
                </tbody>
            </table>
        </div>


    </div>
    <script src="js/index.js"></script>
    <script src="js/filtros.js"></script>
</body>

</html>