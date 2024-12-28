<?php
include 'db.php';
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicializar mensaje de estado
$mensaje = '';

// Función para construir la jerarquía de categorías
function obtenerCategorias($pdo)
{
    // Obtener todas las categorías
    $stmt = $pdo->query("SELECT * FROM categoria");
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Organizar las categorías en un árbol
    $arbol = [];
    foreach ($categorias as $categoria) {
        if ($categoria['categoria_padre_id'] === null) {
            // Categorías principales
            $arbol[$categoria['id_categoria']] = $categoria;
            $arbol[$categoria['id_categoria']]['subcategorias'] = [];
        } else {
            // Subcategorías
            $arbol[$categoria['categoria_padre_id']]['subcategorias'][] = $categoria;
        }
    }
    return $arbol;
}

// Manejo de acciones: agregar o eliminar categorías y subcategorías
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_categoria'])) {
            // Agregar categoría
            $stmt = $pdo->prepare("INSERT INTO categoria (nombre, categoria_padre_id) VALUES (:nombre_categoria, :categoria_padre_id)");
            $stmt->execute([
                ':nombre_categoria' => $_POST['nombre_categoria'],
                ':categoria_padre_id' => !empty($_POST['categoria_padre_id']) ? $_POST['categoria_padre_id'] : null
            ]);
            $mensaje = "Categoría agregada exitosamente.";
        } elseif (isset($_POST['add_categoria_padre'])) {
            // Agregar categoría padre (sin categoría padre)
            $stmt = $pdo->prepare("INSERT INTO categoria (nombre, categoria_padre_id) VALUES (:nombre_categoria, :categoria_padre_id)");
            $stmt->execute([
                ':nombre_categoria' => $_POST['nombre_categoria_padre'] ?? null,
                ':categoria_padre_id' => null // Categoría padre sin un padre
            ]);
            $mensaje = "Categoría padre agregada exitosamente.";
        }
        if (isset($_POST['delete_categoria'])) {
            $id_categoria = $_POST['delete_categoria'];

            // Verificar si hay productos asociados a esta categoría
            $checkProductosStmt = $pdo->prepare("SELECT COUNT(*) FROM productos WHERE categoria_id = :id_categoria");
            $checkProductosStmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
            $checkProductosStmt->execute();
            $productosCount = $checkProductosStmt->fetchColumn();

            if ($productosCount > 0) {
                $mensaje = "No se puede eliminar la categoría porque tiene productos asociados.";
            } else {
                // Si no hay productos, proceder con la eliminación
                $sql = "DELETE FROM categoria WHERE id_categoria = :id_categoria";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
                $stmt->execute();
                $mensaje = "Categoría eliminada exitosamente.";
            }
        }



        // Redirigir para evitar reenvíos en el navegador
        header("Location: " . $_SERVER['PHP_SELF'] . "?message=" . urlencode($mensaje));
        exit;
    } catch (PDOException $e) {
        $mensaje = "Error: " . htmlspecialchars($e->getMessage());
    }
}

// Obtener la jerarquía de categorías
$categoriasJerarquia = obtenerCategorias($pdo);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="modal.css">
    <link rel="stylesheet" href="css/categorias.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <title>Gestión de Categorías</title>
</head>

<body>
    <!-- Barra de navegación -->
    <nav>
        <ul class="nav-links">
            <li class="brand">Shop Inventory</li>
            <li><a href="menu.php">Inicio</a></li>
            <li><a href="usuarios.php">Usuarios</a></li>
            <li><a href="gestion_local.php">Locales</a></li>
            <li><a href="index.php">Inventario</a></li>
            <li><a href="alertas.php">Alertas</a></li>
            <li><a href="categorias.php" class="active">Categorías</a></li>
            <li><a href="proveedores.php">Proveedores</a></li>
            <li class="logout"><a href="../index.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Gestión de Categorías</h1>
        <br><br>
        <!-- Mensaje de éxito o error -->
        <?php if ($mensaje) : ?>
            <div class="alert"><?php echo htmlspecialchars($mensaje); ?></div>
        <?php endif; ?>

        <!-- Botones para abrir los modales -->
        <div class="modal-buttons">
            <button id="openModalPadreBtn" class="btnUpdate">Agregar Categoría </button>
            <button id="openModalBtn" class="btnAdd">Agregar Subcategoria</button>
            <button onclick="location.href='index.php'" class="button-inventario">Volver al Inventario</button>
        </div>

        <!-- Modal para agregar categoría -->
        <div id="myModal" class="modal">
            <div class="modal-content">
                <span class="close" id="closeModalBtn">&times;</span>
                <h2>Agregar Subcategoria</h2>
                <br>
                <form method="POST">
                    <div class="form-group">
                        <label for="nombre_categoria">Nombre de la Subcategoria:</label>
                        <input type="text" name="nombre_categoria" id="nombre_categoria" required>
                    </div>

                    <div class="form-group">
                        <label for="categoria_padre_id">Categoría:</label>
                        <select name="categoria_padre_id" id="categoria_padre_id">
                            <option value="">Seleccionar</option>
                            <?php foreach ($categoriasJerarquia as $categoria) : ?>
                                <option value="<?php echo $categoria['id_categoria']; ?>"><?php echo $categoria['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" name="add_categoria">Agregar Subcategoria</button>
                </form>
            </div>
        </div>

        <!-- Modal para agregar categoría padre -->
        <div id="myModalPadre" class="modal">
            <div class="modal-content">
                <span class="close" id="closeModalPadreBtn">&times;</span>
                <h2>Agregar Categoría</h2>
                <br><br>
                <form method="POST">
                    <div class="form-group">
                        <label for="nombre_categoria_padre">Nombre de la Categoría :</label>
                        <br>
                        <input type="text" name="nombre_categoria_padre" id="nombre_categoria_padre" required>
                        <br>
                    </div>

                    <button class="agregar-categoria" type="submit" name="add_categoria_padre">Agregar Categoría</button>
                </form>
            </div>
        </div>

        <!-- Listado de Categorías -->
        <div id="categorias">
            <ul class="categoria-lista">
                <?php foreach ($categoriasJerarquia as $categoria) : ?>
                    <li class="categoria-item" id="categoria-<?php echo $categoria['id_categoria']; ?>">
                        <span class="toggle-btn" onclick="toggleSubcategorias(<?php echo $categoria['id_categoria']; ?>)">+</span>
                        <span class="categoria-nombre"><?php echo htmlspecialchars($categoria['nombre']); ?></span>
                        <!-- Subcategorías de la categoría -->
                        <?php if (!empty($categoria['subcategorias'])) : ?>
                            <ul class="subcategorias" id="subcategorias-<?php echo $categoria['id_categoria']; ?>">
                                <?php foreach ($categoria['subcategorias'] as $subcategoria) : ?>
                                    <li><?php echo htmlspecialchars($subcategoria['nombre']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                        <!-- Botón para eliminar categoría -->
                        <form method="POST" style="display:inline;">
                            <button class="btn-eli" type="submit" name="delete_categoria" value="<?php echo $categoria['id_categoria']; ?>"
                                onclick="return confirm('¿Estás seguro de que deseas eliminar esta categoría?');">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>


                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <!-- Archivos de JavaScript -->
    <script src="js/categorias.js"></script>
</body>

</html>