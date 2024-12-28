<?php
include "db.php";  // Conexión a la base de datos

session_start();  // Para manejar las sesiones

// Función para validar el RUT
function validarRut($rut)
{
    $rut = preg_replace('/[^k0-9]/i', '', $rut);
    if (!$rut || strlen($rut) < 8) {
        return false;
    }

    $dv = substr($rut, -1);
    $numeros = substr($rut, 0, strlen($rut) - 1);
    $i = 2;
    $suma = 0;
    foreach (array_reverse(str_split($numeros)) as $v) {
        if ($i == 8) $i = 2;
        $suma += $v * $i;
        ++$i;
    }
    $dvr = 11 - ($suma % 11);

    if ($dvr == 11) $dvr = 0;
    if ($dvr == 10) $dvr = 'K';
    if ($dvr == strtoupper($dv)) return true;
    return false;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Recoger y sanitizar los datos del formulario
    $rut = $_POST["rut"];
    $tipo_empleado = $_POST["tipo_empleado"] ?? null;
    $nombre = $_POST["nombre"] ?? null;
    $apellido = $_POST["apellido"] ?? null;
    $direccion_empleado = $_POST["direccion_empleado"] ?? null;
    $telefono = $_POST["telefono"] ?? null;
    $email = $_POST["email"] ?? null;
    $local_id = isset($_POST['local_id']) ? intval($_POST['local_id']) : null;

    // Validar el RUT
    if (!validarRut($rut)) {
        header("Location: empleados.php?error=4"); // Redirigir con mensaje de error
        exit;
    }

    // Verificar si los campos obligatorios están completos
    if (empty($rut) || empty($tipo_empleado) || empty($nombre) || empty($apellido) || empty($local_id)) {
        header("Location: empleados.php?error=1"); // Redirigir con mensaje de error
        exit;
    }

    // Verificar si el RUT ya está registrado
    $sql_check = "SELECT COUNT(*) FROM empleado WHERE rut = :rut";
    $stmt_check = $pdo->prepare($sql_check);
    $stmt_check->execute([":rut" => $rut]);
    $count = $stmt_check->fetchColumn();

    if ($count > 0) {
        header("Location: empleados.php?error=2"); // Redirigir con mensaje de error
        exit;
    }

    // Preparar la consulta SQL para insertar el nuevo empleado
    $sql_insert = "INSERT INTO empleado (rut, tipo_empleado, nombre, apellido, direccion_empleado, telefono, email, local_id) 
                   VALUES (:rut, :tipo_empleado, :nombre, :apellido, :direccion_empleado, :telefono, :email, :local_id)";
    $stmt = $pdo->prepare($sql_insert);

    try {
        // Ejecutar la consulta con los datos proporcionados
        $stmt->execute([
            ":rut" => $rut,
            ":tipo_empleado" => $tipo_empleado,
            ":nombre" => $nombre,
            ":apellido" => $apellido,
            ":direccion_empleado" => $direccion_empleado,
            ":telefono" => $telefono,
            ":email" => $email,
            ":local_id" => $local_id
        ]);

        header("Location: empleados.php?success=1"); // Redirigir con mensaje de éxito
        exit;
    } catch (PDOException $e) {
        // Manejo de errores al agregar el empleado
        header("Location: empleados.php?error=3"); // Redirigir con mensaje de error
        exit;
    }
}

$sql = "SELECT e.*, l.nombre AS local_nombre FROM empleado e
        LEFT JOIN local l ON e.local_id = l.id_local";


$sql_empleados = "SELECT e.*, l.nombre AS local_nombre
                  FROM empleado e
                  LEFT JOIN local l ON e.local_id = l.id_local
                  ORDER BY e.id_empleado ASC";
$stmt_empleados = $pdo->prepare($sql_empleados);
$stmt_empleados->execute();
$result_empleados = $stmt_empleados->fetchAll(PDO::FETCH_ASSOC);


// Reiniciar el ID a 1 si no hay empleados en la base de datos
if (empty($result_empleados)) {
    // Eliminar todos los registros
    $sql_delete_all = "DELETE FROM empleado";
    $stmt_delete_all = $pdo->prepare($sql_delete_all);
    $stmt_delete_all->execute();

    // Reiniciar el contador AUTO_INCREMENT
    $sql_reset_auto_increment = "ALTER TABLE empleado AUTO_INCREMENT = 1";
    $stmt_reset_auto_increment = $pdo->prepare($sql_reset_auto_increment);
    $stmt_reset_auto_increment->execute();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="empleados.css">
    <link rel="stylesheet" href="usuarios.css">
    <link rel="stylesheet" href="modal.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

    <title>Empleados</title>
</head>

<body>
    <nav>
        <ul class="nav-links">
            <li class="brand">Shop Inventory</li>
            <li><a href="menu.php">Inicio</a></li>
            <li><a href="usuarios.php">Usuarios</a></li>
            <li><a href="index.php">Inventario</a></li>
            <li><a href="alertas.php">Alertas</a></li>
            <li><a href="categorias.php">Categorías</a></li>
            <li><a href="proveedores.php">Proveedores</a></li>
            <li class="logout"><a href="../index.php">Cerrar Sesión</a></li>
        </ul>
    </nav>

    <div class="container-employee">
        <!-- Mostrar mensajes de error o éxito -->
        <?php if (isset($_GET['error'])): ?>
            <div class="mensaje mensaje-error">
                <?php
                if ($_GET['error'] == 1) {
                    echo "¡Todos los campos son obligatorios! Por favor, complete todos los campos requeridos.";
                } elseif ($_GET['error'] == 2) {
                    echo "¡El RUT ya está registrado! Por favor, ingrese un RUT único.";
                } elseif ($_GET['error'] == 3) {
                    echo "¡Error al agregar el empleado! Por favor, inténtelo nuevamente.";
                } elseif ($_GET['error'] == 4) {
                    echo "¡RUT no válido! Por favor, ingrese un RUT correcto.";
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="mensaje mensaje-exito">
                ¡Empleado agregado exitosamente! El empleado se ha registrado correctamente.
            </div>
        <?php endif; ?>

        <h1>Listado de Empleados</h1>
        <table border="1" class="empleados-table">
            <br>
            <a href="usuarios.php" class="back-btn">Volver</a>

            <thead>
                <tr>
                    <th>Id</th>
                    <th>Rut</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Tipo Empleado</th>
                    <th>Dirección</th>
                    <th>Teléfono</th>
                    <th>Email</th>
                    <th>Local</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (count($result_empleados) > 0) {
                    foreach ($result_empleados as $row) {
                        echo "<tr>
            <td>" . htmlspecialchars($row["id_empleado"]) . "</td>
            <td>" . htmlspecialchars($row["rut"]) . "</td>
            <td>" . htmlspecialchars($row["nombre"]) . "</td>
            <td>" . htmlspecialchars($row["apellido"]) . "</td>
            <td>" . htmlspecialchars($row["tipo_empleado"]) . "</td>
            <td>" . htmlspecialchars($row["direccion_empleado"]) . "</td>
            <td>" . htmlspecialchars($row["telefono"]) . "</td>
            <td>" . htmlspecialchars($row["email"]) . "</td>
            <td>" . (isset($row["local_nombre"]) ? htmlspecialchars($row["local_nombre"]) : "No asignado") . "</td>

            <td>
                <!-- Botón de Editar con ícono -->
                <button class='edit-btn' data-id='" . $row["id_empleado"] . "' data-nombre='" . $row["nombre"] . "' data-apellido='" . $row["apellido"] . "' data-tipo_empleado='" . $row["tipo_empleado"] . "' data-direccion='" . $row["direccion_empleado"] . "' data-telefono='" . $row["telefono"] . "' data-email='" . $row["email"] . "'>
                    <i class='fas fa-edit'></i>
                </button>

                <!-- Botón de Eliminar con ícono -->
                <button class='delete-btn' data-id='" . $row["id_empleado"] . "'>
                    <i class='fas fa-trash-alt'></i>
                </button>
            </td>
        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='10'>No hay empleados disponibles</td></tr>";
                }
                ?>

            </tbody>
        </table>
    </div>

    <!-- Modal para editar empleado -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-change" onclick="document.getElementById('editModal').style.display = 'none';">&times;</span>
            <h2>Editar Empleado</h2>
            <form id="editFormContainer" method="POST">
                <input type="hidden" id="editEmpleadoId" name="userId">

                <label for="editNombre">Nombre:</label>
                <input type="text" id="editNombre" name="user" required>
                <br><br>

                <label for="editApellido">Apellido:</label>
                <input type="text" id="editApellido" name="apellido" required>
                <br><br>

                <label for="editTipoEmpleado">Tipo de Empleado:</label>
                <select id="editTipoEmpleado" name="tipo_empleado" required>
                    <option value="">Seleccione tipo de empleado</option>
                    <option value="Administrador">Administrador</option>
                    <option value="Vendedor">Vendedor</option>
                </select>
                <br><br>

                <label for="editDireccion">Dirección:</label>
                <input type="text" id="editDireccion" name="direccion_empleado" required>
                <br><br>

                <label for="editTelefono">Teléfono:</label>
                <input type="text" id="editTelefono" name="telefono" required>
                <br><br>

                <label for="editEmail">Email:</label>
                <input type="email" id="editEmail" name="email" required>
                <br><br>

                <button type="submit">Guardar Cambios</button>
            </form>
        </div>
    </div>

    <script src="js/empleados.js"></script>
</body>

</html>