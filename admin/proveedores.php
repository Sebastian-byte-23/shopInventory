<?php
include 'db.php';

$mensaje_error = ""; // Variable para almacenar mensajes de error
$nombre_proveedor_val = ""; // Variables para almacenar valores predeterminados
$numero_movil_val = "";

// Manejo de acciones: agregar, eliminar o editar proveedores
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_proveedor = isset($_POST['nombre_proveedor']) ? trim($_POST['nombre_proveedor']) : "";
    $numero_movil = isset($_POST['numero_movil']) ? trim($_POST['numero_movil']) : "";

    // Asigna los valores introducidos para mantenerlos en los campos
    $nombre_proveedor_val = $nombre_proveedor;
    $numero_movil_val = $numero_movil;

    // Agregar proveedor: requiere ambos campos 
    if (isset($_POST['add'])) {
        if (empty($nombre_proveedor)) {
            $mensaje_error = "El nombre del proveedor es obligatorio.";
        } elseif (strlen($nombre_proveedor) > 45) {
            $mensaje_error = "El nombre del proveedor no debe exceder los 45 caracteres.";
        } elseif (empty($numero_movil)) {
            $mensaje_error = "El número móvil es obligatorio.";
        } elseif (!preg_match('/^[0-9]{9}$/', $numero_movil)) {
            $mensaje_error = "El número móvil debe contener exactamente 9 dígitos numéricos.";
        } else {
            // Inserta los datos en la base de datos
            $stmt = $pdo->prepare("INSERT INTO proveedor (nombre_proveedor, numero_movil) VALUES (?, ?)");
            $stmt->execute([$nombre_proveedor, $numero_movil]);

            // Evita la duplicidad redirigiendo después del éxito
            header("Location: proveedores.php?success=1"); // Redirige a la página deseada
            exit; // Finaliza el script después de la redirección
        }
    }


    // Eliminar proveedor: requiere el nombre del proveedor
    if (isset($_POST['delete'])) {
        if (!empty($nombre_proveedor)) {
            // Obtener id_proveedor basado en el nombre
            $stmt = $pdo->prepare("SELECT id_proveedor FROM proveedor WHERE nombre_proveedor = ?");
            $stmt->execute([$nombre_proveedor]);
            $id_proveedor = $stmt->fetchColumn();

            // Comprobar si existen productos asociados a este proveedor
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM productos_has_proveedor WHERE proveedor_id = ?");
            $stmt->execute([$id_proveedor]);
            $productos_asociados = $stmt->fetchColumn();

            if ($productos_asociados > 0) {
                $mensaje_error = "No se puede eliminar el proveedor porque existen productos asociados a él.";
            } else {
                // Procede a eliminar si no hay productos asociados
                $stmt = $pdo->prepare("DELETE FROM proveedor WHERE nombre_proveedor = ?");
                $stmt->execute([$nombre_proveedor]);
                $pdo->exec("ALTER TABLE proveedor AUTO_INCREMENT = 1;");
            }
        } else {
            $mensaje_error = "Debe proporcionar el nombre del proveedor para eliminarlo.";
        }
    }

    // Verifica si se ha enviado la solicitud de edición del proveedor
    if (isset($_POST['edit'])) {

        // Obtiene y limpia los valores enviados a través del formulario
        $id_proveedor = isset($_POST['id_proveedor']) ? trim($_POST['id_proveedor']) : "";
        $nombre_proveedor = isset($_POST['nombre_proveedor']) ? trim($_POST['nombre_proveedor']) : "";
        $numero_movil = isset($_POST['numero_movil']) ? trim($_POST['numero_movil']) : "";

        // Verifica si los campos necesarios están vacíos
        if (empty($id_proveedor) || empty($nombre_proveedor) || empty($numero_movil)) {
            $mensaje_error = "Todos los campos son obligatorios para la edición."; // Si algún campo está vacío, muestra un mensaje de error
        } else {
            // Si todos los campos son válidos, realiza la actualización del proveedor
            try {
                $stmt = $pdo->prepare("UPDATE proveedor SET nombre_proveedor = ?, numero_movil = ? WHERE id_proveedor = ?");
                $stmt->execute([$nombre_proveedor, $numero_movil, $id_proveedor]);

                // Si la actualización se realiza correctamente, muestra un mensaje de éxito
                $mensaje_exito = "Proveedor actualizado correctamente.";
            } catch (PDOException $e) {
                // Si ocurre algún error durante la ejecución de la consulta, captura el error y muestra un mensaje
                $mensaje_error = "Error al actualizar el proveedor: " . $e->getMessage();
            }
        }
    }
}

// Obtener la lista de proveedores
$proveedores = $pdo->query("SELECT * FROM proveedor")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/proveedores.css">
    <link rel="stylesheet" href="nav.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">

    <title>Gestión de Proveedores</title>
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
            <li><a href="categorias.php">Categorías</a></li>
            <li><a href="proveedores.php" class="active">Proveedores</a></li>
            <li class="logout"><a href="../index.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <div class="container">
        <h1>Gestión de Proveedores</h1>
        <br><br>

        <br>
        <!-- Botón para abrir el modal de agregar proveedor -->
        <button class="open-add-modal">Agregar Proveedor</button>
        <button onclick="location.href='index.php'" class="button-i">Volver al Inventario</button>

        <!-- Tabla de proveedores -->
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre Proveedor</th>
                    <th>Número Móvil</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($proveedores as $proveedor): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($proveedor['id_proveedor']); ?></td>
                        <td><?php echo htmlspecialchars($proveedor['nombre_proveedor']); ?></td>
                        <td><?php echo htmlspecialchars($proveedor['numero_movil']); ?></td>
                        <td>
                            <button class="btn-edit" data-provider-name="<?php echo htmlspecialchars($proveedor['nombre_proveedor']); ?>" data-provider-mobile="<?php echo htmlspecialchars($proveedor['numero_movil']); ?>" data-provider-id="<?php echo htmlspecialchars($proveedor['id_proveedor']); ?>">
                                <i class="fas fa-edit" title="Editar proveedor"></i>
                            </button>


                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="nombre_proveedor" value="<?php echo htmlspecialchars($proveedor['nombre_proveedor']); ?>">
                                <button class="eliminar-btn" type="submit" name="delete" onclick="return confirm('¿Estás seguro de que quieres eliminar este proveedor?');">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>

                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal para agregar proveedor -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Agregar Proveedor</h2>
            <br><br>

            <?php if ($mensaje_error): ?>
                <div class="error-message"><?php echo htmlspecialchars($mensaje_error); ?></div>
            <?php endif; ?>

            <form id="addForm" method="POST">
                <label>Nombre del Proveedor:</label>
                <input type="text" name="nombre_proveedor" id="addNombreProveedor" value="<?php echo htmlspecialchars($nombre_proveedor_val); ?>" required>
                <label>Número Móvil:</label>
                <input type="text" name="numero_movil" id="addNumeroMovil" value="<?php echo htmlspecialchars($numero_movil_val); ?>" required>
                <button type="submit" name="add">Agregar</button>
            </form>
        </div>
    </div>

    <!-- Modal para editar proveedor -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Editar Proveedor</h2>
            <form id="editForm" method="POST">
                <input type="hidden" name="id_proveedor" id="editIdProveedor" required>
                <label>Nombre del Proveedor:</label>
                <input type="text" name="nombre_proveedor" id="editNombreProveedor" required>
                <label>Número Móvil:</label>
                <input type="text" name="numero_movil" id="editNumeroMovil" required>
                <button type="submit" name="edit">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const addModal = document.getElementById('addModal');
            const editModal = document.getElementById('editModal');
            const openAddModalButton = document.querySelector('.open-add-modal');
            const closeModalButtons = document.querySelectorAll('.close');

            // Verificar si los elementos existen antes de agregar eventos
            if (!addModal || !editModal || !openAddModalButton) {
                console.error('Faltan elementos importantes: addModal, editModal o openAddModalButton');
                return; // Salir si los elementos no existen
            }

            // Obtener el mensaje de error desde PHP
            const mensajeError = <?php echo json_encode($mensaje_error); ?>;
            if (mensajeError && addModal) {
                const errorMessageDiv = addModal.querySelector('.error-message');
                if (errorMessageDiv) {
                    errorMessageDiv.textContent = mensajeError;
                    addModal.style.display = 'block'; // Mostrar el modal de agregar si hay error
                }
            }

            // Abre el modal de agregar proveedor
            openAddModalButton.addEventListener('click', function() {
                if (addModal) {
                    addModal.style.display = 'block';
                }
            });

            // Cerrar los modales
            closeModalButtons.forEach(button => {
                button.addEventListener('click', function() {
                    if (addModal) addModal.style.display = 'none';
                    if (editModal) editModal.style.display = 'none';
                });
            });

            // Cerrar el modal si el usuario hace clic fuera de él
            window.addEventListener('click', function(event) {
                if (event.target === addModal) {
                    addModal.style.display = 'none';
                }
                if (event.target === editModal) {
                    editModal.style.display = 'none';
                }
            });

            // Manejo de clic en los botones de edición
            const openModalButtons = document.querySelectorAll('.btn-edit');
            openModalButtons.forEach(button => {
                button.addEventListener('click', function(event) {
                    event.preventDefault();
                    const providerName = this.getAttribute('data-provider-name');
                    const providerMobile = this.getAttribute('data-provider-mobile');
                    const providerId = this.getAttribute('data-provider-id');

                    // Llenar los campos del modal de edición con los datos del proveedor
                    document.getElementById('editIdProveedor').value = providerId;
                    document.getElementById('editNombreProveedor').value = providerName;
                    document.getElementById('editNumeroMovil').value = providerMobile;

                    // Mostrar el modal de edición
                    const editModal = document.getElementById('editModal');
                    if (editModal) {
                        editModal.style.display = 'block'; // Cambiar el display a block para mostrar el modal
                    }
                });
            });


        });
    </script>

</body>

</html>