<?php
// Incluir la conexión a la base de datos con PDO
include 'db.php';

// Validaciones para los campos antes de insertar
function validarFormulario($tipo_local, $nombre, $direccion, $telefono, $email)
{
    // Verificar que no haya campos vacíos
    if (empty($tipo_local) || empty($nombre) || empty($direccion) || empty($telefono) || empty($email)) {
        return "Todos los campos son obligatorios.";
    }

    // Validar el formato del teléfono (opcional, puedes ajustarlo según tus necesidades)
    if (!preg_match("/^[0-9]{7,15}$/", $telefono)) {
        return "El número de teléfono no es válido. Debe tener entre 7 y 15 dígitos.";
    }

    // Validar el formato del email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return "El correo electrónico no es válido.";
    }

    return true;
}

// Insertar un nuevo local con el id_local calculado
if (isset($_POST['agregar'])) {
    $tipo_local = $_POST['tipo_local'];
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];

    // Verificar si el local ya existe por nombre o email
    $sql_check = "SELECT COUNT(*) FROM local WHERE nombre = :nombre OR email = :email";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([
        ':nombre' => $nombre,
        ':email' => $email
    ]);

    $existeLocal = $stmt_check->fetchColumn();

    if ($existeLocal > 0) {
        echo "<p style='color:red;'>El local con ese nombre o correo electrónico ya existe.</p>";
    } else {
        // Obtener el último id_local y calcular el nuevo id
        $sql = "SELECT MAX(id_local) FROM local";
        $stmt = $pdo->query($sql);
        $ultimoId = $stmt->fetchColumn();
        $id_local = $ultimoId + 1;

        // Validación de los datos
        $validacion = validarFormulario($tipo_local, $nombre, $direccion, $telefono, $email);
        if ($validacion === true) {
            $sql_insert = "INSERT INTO local (id_local, tipo_local, nombre, direccion, telefono, email) 
                        VALUES (:id_local, :tipo_local, :nombre, :direccion, :telefono, :email)";
            $stmt_insert = $pdo->prepare($sql_insert);
            $stmt_insert->execute([
                ':id_local' => $id_local,
                ':tipo_local' => $tipo_local,
                ':nombre' => $nombre,
                ':direccion' => $direccion,
                ':telefono' => $telefono,
                ':email' => $email
            ]);
        } else {
            echo "<p style='color:red;'>$validacion</p>";
        }
    }
}

if (isset($_POST['editar'])) {
    // Obtener los datos del formulario
    $tipo_local = $_POST['tipo_local'];
    $nombre = $_POST['nombre'];
    $direccion = $_POST['direccion'];
    $telefono = $_POST['telefono'];
    $email = $_POST['email'];
    $id_local = $_POST['id_local'];

    // Verificar si el nombre o email ya están en uso, excluyendo el local actual
    $sql_check = "SELECT COUNT(*) FROM local WHERE (nombre = :nombre OR email = :email) AND id_local != :id_local";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([
        ':nombre' => $nombre,
        ':email' => $email,
        ':id_local' => $id_local
    ]);
    $existeLocal = $stmt_check->fetchColumn();

    if ($existeLocal > 0) {
        echo json_encode(["status" => "error", "message" => "El local con ese nombre o correo electrónico ya existe."]);
    } else {
        // Si no existe duplicado, proceder a actualizar el local
        $sql_update = "UPDATE local 
                        SET tipo_local = :tipo_local, 
                            nombre = :nombre, 
                            direccion = :direccion, 
                            telefono = :telefono, 
                            email = :email 
                        WHERE id_local = :id_local";

        $stmt_update = $pdo->prepare($sql_update);
        $stmt_update->execute([
            ':id_local' => $id_local,
            ':tipo_local' => $tipo_local,
            ':nombre' => $nombre,
            ':direccion' => $direccion,
            ':telefono' => $telefono,
            ':email' => $email
        ]);

        echo json_encode([
            "status" => "success",
            "message" => "Local actualizado correctamente."
        ]);
    }
    exit;
}


// Eliminar un local
if (isset($_POST['borrar'])) {
    if (isset($_POST['id_local']) && !empty($_POST['id_local'])) {
        $id_local = $_POST['id_local'];

        // Validar si el ID es un número entero
        if (filter_var($id_local, FILTER_VALIDATE_INT)) {

            // Verificar si el local está relacionado con empleados
            $sql_check = "SELECT COUNT(*) FROM empleado WHERE local_id = :id_local"; // Suponiendo que 'local_id' existe en 'empleados'
            $stmt_check = $pdo->prepare($sql_check);
            $stmt_check->execute([':id_local' => $id_local]);
            $relatedEmployees = $stmt_check->fetchColumn();

            if ($relatedEmployees > 0) {
                // Si el local tiene empleados relacionados, no permitir la eliminación
                $mensaje = "No se puede eliminar el local porque está asociado con uno o más empleados."; // Guardar el mensaje de error
            } else {
                // Si no está relacionado, proceder con la eliminación
                $sql_delete = "DELETE FROM local WHERE id_local = :id_local";
                $stmt_delete = $pdo->prepare($sql_delete);
                $stmt_delete->execute([':id_local' => $id_local]);
                $mensaje = "Local eliminado con éxito."; // Mensaje de éxito
            }
        } else {
            $mensaje = "El ID del local no es válido."; // Mensaje de error si no es un número entero
        }
    } else {
        $mensaje = "No se ha proporcionado un ID de local para eliminar."; // Mensaje si no se proporciona un ID
    }
}

// Obtener los registros de la tabla
$sql = "SELECT * FROM local";
$stmt = $pdo->query($sql);
$locales = $stmt->fetchAll(PDO::FETCH_ASSOC);


?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">

    <link rel="stylesheet" type="text/css" href="nav.css">
    <link rel="stylesheet" type="text/css" href="css/local.css">
    <!-- Font Awesome CDN (Agrega esto en la sección <head> de tu HTML) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <title>Gestión de Locales</title>
</head>

<body>
    <nav>
        <ul class="nav-links">
            <li class="brand">Shop Inventory</li>
            <li><a href="menu.php">Inicio</a></li>
            <li><a href="usuarios.php">Usuarios</a></li>
            <li><a href="gestion_local.php" class="active">Locales</a></li>
            <li><a href="index.php">Inventario</a></li>
            <li><a href="alertas.php">Alertas</a></li>
            <li><a href="categorias.php">Categorías</a></li>
            <li><a href="proveedores.php">Proveedores</a></li>
            <li class="logout"><a href="../index.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <!-- Contenedor principal -->
    <div class="container">
        <h2>Locales Existentes</h2>

        <div class="mensaje-container">
            <?php
            // Verifica si el mensaje de error o éxito está establecido
            if (isset($mensaje)) {
                echo "<div class='mensaje'>$mensaje</div>"; // Muestra el mensaje dentro del contenedor
            }
            ?>
        </div>

        <button class="btn-agregar" onclick="abrirModalAgregar()">Agregar Local</button>

        <table class="tabla-locales">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tipo de Local</th>
                    <th>Nombre</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($locales as $fila) : ?>
                    <tr>
                        <td><?= htmlspecialchars($fila['id_local']) ?></td>
                        <td id="tipo_local_<?= $fila['id_local'] ?>"><?= htmlspecialchars($fila['tipo_local']) ?></td>
                        <td id="nombre_<?= $fila['id_local'] ?>"><?= htmlspecialchars($fila['nombre']) ?></td>
                        <td id="direccion_<?= $fila['id_local'] ?>"><?= htmlspecialchars($fila['direccion']) ?></td>
                        <td id="telefono_<?= $fila['id_local'] ?>"><?= htmlspecialchars($fila['telefono']) ?></td>
                        <td id="email_<?= $fila['id_local'] ?>"><?= htmlspecialchars($fila['email']) ?></td>
                        <td>
                            <button class="edit-btn" onclick="abrirModalEditar(<?= htmlspecialchars(json_encode($fila)) ?>)">
                                <i class="fas fa-edit"></i>
                            </button>


                            <form method="post" action="gestion_local.php" style="display:inline;">
                                <input type="hidden" name="id_local" value="<?= htmlspecialchars($fila['id_local']) ?>">
                                <button class="delete-btn" type="submit" name="borrar">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>


        </table>
    </div>

    <!-- Modal para editar local -->
    <div id="modalEditar" class="modal" style="display:none;">
        <div class="modal-contenido">
            <span onclick="cerrarModal('modalEditar')" style="cursor:pointer;">&times;</span>
            <h2>Editar Local</h2>
            <form id="formEditar">
                <input type="hidden" id="id_local" name="id_local"> <!-- ID del local -->
                <label for="tipo_local">Tipo de Local:</label>
                <select id="tipo_local" name="tipo_local" required>
                    <option value="">Seleccione el tipo de local</option>
                    <option value="Casa Matriz">Casa Matriz</option>
                    <option value="Sucursal">Sucursal</option>
                </select>
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" required>
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" required>
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
                <button type="button" onclick="editarLocal()">Guardar Cambios</button>
            </form>


        </div>
    </div>

    <!-- Modal para agregar local -->
    <div id="modalAgregar" style="display:none;" class="modal">
        <div class="modal-contenido">
            <span onclick="cerrarModal('modalAgregar')" style="cursor:pointer;">&times;</span>
            <h2>Agregar Local</h2>
            <form method="post" action="gestion_local.php">
                <label>Tipo de Local:</label>
                <select name="tipo_local" required>
                    <option value="sucursal">Sucursal</option>
                    <option value="casa matriz">Casa Matriz</option>
                </select><br>
                <label>Nombre:</label>
                <input type="text" name="nombre" required><br>
                <label>Dirección:</label>
                <input type="text" name="direccion" required><br>
                <label>Teléfono:</label>
                <input type="text" name="telefono" required><br>
                <label>Email:</label>
                <input type="email" name="email" required><br>
                <button type="submit" name="agregar">Agregar</button>
            </form>
        </div>
    </div>

    <script src="js/locales.js"></script>
</body>


</html>