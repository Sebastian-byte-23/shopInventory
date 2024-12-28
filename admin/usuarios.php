<?php
include "db.php";
include "CreateUser.php";

// Recibir mensajes de la sesión y luego eliminarlos para evitar que aparezcan en futuros refrescos
$error_usuario = $_SESSION['error_usuario'] ?? '';
$error_contraseña = $_SESSION['error_contraseña'] ?? '';
$success_message = $_SESSION['success_message'] ?? '';

unset($_SESSION['error_usuario'], $_SESSION['error_contraseña'], $_SESSION['success_message']);
// Restablecer el AUTO_INCREMENT del id_usuarios
$pdo->query("ALTER TABLE usuarios AUTO_INCREMENT = 1");

// Consultar la lista de usuarios
$sql_usuarios = "SELECT id_usuarios, user, tipo_empleado FROM usuarios ORDER BY id_usuarios ASC";
$stmt = $pdo->prepare($sql_usuarios);
$stmt->execute();
$result_usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Consultar los empleados para el combo de selección
$sql_empleados = "SELECT apellido, tipo_empleado FROM empleado";
$stmt_empleados = $pdo->prepare($sql_empleados);
$stmt_empleados->execute();
$result_empleados = $stmt_empleados->fetchAll(PDO::FETCH_ASSOC);

// Consultar usuarios con tipo de empleado
$sql = "SELECT u.id_usuarios, u.user, e.tipo_empleado
        FROM usuarios u
        LEFT JOIN empleado e ON u.empleado_id = e.id_empleado";
$result_usuarios = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Consulta para obtener los locales
$sql = "SELECT id_local, nombre FROM local";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$locales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener usuarios junto con el nombre del empleado
$stmt = $pdo->prepare("SELECT u.id_usuarios, u.user, u.tipo_empleado, e.nombre AS empleado_nombre 
                        FROM usuarios u
                        LEFT JOIN empleado e ON u.empleado_id = e.id_empleado");
$stmt->execute();
$result_usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="nav.css">
    <link rel="stylesheet" href="usuarios.css">
    <link rel="stylesheet" href="css/editar_usuario.css">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">


    <title>Usuarios</title>
</head>

<body>

    <nav>
        <ul class="nav-links">
            <li class="brand">Shop Inventory</li>
            <li><a href="menu.php">Inicio</a></li>
            <li><a href="usuarios.php" class="active">Usuarios</a></li>
            <li><a href="gestion_local.php">Locales</a></li>
            <li><a href="index.php">Inventario</a></li>
            <li><a href="alertas.php">Alertas</a></li>
            <li><a href="categorias.php">Categorías</a></li>
            <li><a href="proveedores.php">Proveedores</a></li>
            <li class="logout"><a href="../index.php">Cerrar Sesión</a></li>
        </ul>
    </nav>
    <div class="container1">
        <section id="user-list">
            <!-- Contenedor para el mensaje de error o éxito -->
            <div id="message" class="message" style="display:none;">
                <p id="messageText"></p>
            </div>
            <h1 class="usuario">Usuarios</h1>
            <br>
            <!-- Botón para abrir el modal -->
            <button class="add-user" onclick="openModal()">Agregar Usuario</button>

            <!-- Modal -->
            <div id="myModal" class="modal">
                <div class="modal-content">
                    <span class="close" onclick="closeModal()">&times;</span>
                    <form name="f1" class="form-login" action="CreateUser.php" method="post">
                        <h2>Registro</h2>

                        <label for="usuario">Usuario</label>
                        <input type="text" class="controls" name="usuario" required>
                        <?php if ($error_usuario): ?>
                            <p class="error"><?php echo $error_usuario; ?></p>
                        <?php endif; ?>

                        <label for="c1">Contraseña</label>
                        <input type="password" class="controls" name="c1" required>

                        <label for="c2">Repita contraseña</label>
                        <input type="password" class="controls" name="c2" required>

                        <!-- Dropdown de Empleado -->
                        <label for="empleado_id">Empleado</label>
                        <select name="empleado_id" id="empleado_id" class="controls" onchange="updateTipoEmpleado()" required>
                            <option value="">Seleccione un empleado</option>
                            <?php foreach ($empleados as $empleado): ?>
                                <option value="<?php echo $empleado['id_empleado']; ?>" data-tipo="<?php echo $empleado['tipo_empleado']; ?>">
                                    <?php echo $empleado['nombre']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <!-- Campo de Tipo de Empleado -->
                        <label for="tipo_empleado">Tipo de Empleado</label>
                        <input type="text" id="tipo_empleado" name="tipo_empleado" class="controls" readonly required>

                        <?php if ($error_contraseña): ?>
                            <p class="error"><?php echo $error_contraseña; ?></p>
                        <?php endif; ?>
                        <?php if ($success_message): ?>
                            <p class="success"><?php echo $success_message; ?></p>
                        <?php endif; ?>

                        <input type="submit" class="buttons-registrar" name="bt1" value="Registrar">
                    </form>
                </div>
            </div>

            <div id="editUserModal" class="modal" style="display:none;">
                <div class="modal-content-user">
                    <span class="close" onclick="hideModal('editUserModal')">&times;</span>
                    <form id="editUserForm" action="cambiar_clave.php" method="POST">
                        <h2>Editar Usuario</h2>

                        <label for="edit_id">ID</label>
                        <input type="text" id="edit_id" name="id" readonly>

                        <label for="edit_user">Usuario</label>
                        <input type="text" id="edit_user" name="usuario" required>

                        <label for="edit_tipo_empleado">Tipo de Empleado</label>
                        <select id="edit_tipo_empleado" name="tipo_empleado" required>
                            <option value="Administrador">Administrador</option>
                            <option value="Vendedor">Vendedor</option>
                        </select>

                        <label for="new_password">Nueva Contraseña</label>
                        <input type="password" id="new_password" name="new_password">

                        <label for="confirm_password">Confirmar Contraseña</label>
                        <input type="password" id="confirm_password" name="confirm_password">

                        <input type="submit" value="Guardar cambios">

                        <!-- Contenedor para mostrar el mensaje de éxito o error -->
                        <div id="response-message" class="response-message"></div>
                    </form>
                </div>
            </div>


            <button id="addEmployeeBtn">Agregar Empleado</button>
            <button id="viewEmployeesBtn">Ver Empleados</button> <!-- Nuevo botón para ver empleados -->
            <br><br>
            <table border="1" class="usuarios-table">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Empleado</th> <!-- Columna para mostrar el nombre del empleado -->
                        <th>Usuario</th>
                        <th>Tipo de Empleado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (count($result_usuarios) > 0) {
                        foreach ($result_usuarios as $row) {
                            echo "<tr>
            <td>" . htmlspecialchars($row["id_usuarios"]) . "</td>
            <td>" . htmlspecialchars($row["empleado_nombre"]) . "</td> <!-- Nombre del empleado relacionado -->
            <td>" . htmlspecialchars($row["user"]) . "</td>
            <td>" . htmlspecialchars($row["tipo_empleado"]) . "</td>

            <td>
                <!-- Botón de Editar con solo el ícono -->
                <button class='edit-btn' data-id='" . htmlspecialchars($row["id_usuarios"]) . "' data-user='" . htmlspecialchars($row["user"]) . "' data-tipo_empleado='" . htmlspecialchars($row["tipo_empleado"]) . "'>
                    <i class='fas fa-edit'></i>
                </button>

                <!-- Botón de Eliminar con solo el ícono -->
                <button class='delete-btn' data-id='" . htmlspecialchars($row["id_usuarios"]) . "'>
                    <i class='fas fa-trash-alt'></i>
                </button>
            </td>
        </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>No hay usuarios disponibles</td></tr>"; // Se ajusta el colspan para incluir la nueva columna
                    }
                    ?>
                </tbody>
            </table>
        </section>
    </div>
    </div>
    <!-- Modal para agregar empleado -->
    <div id="addEmployeeModal" class="modal">
        <div class="modal-content">
            <span class="close-add-employee" onclick="document.getElementById('addEmployeeModal').style.display='none'">&times;</span>
            <div class="modal-header">
                <h2>Agregar Empleado</h2>
            </div>
            <form id="addEmployeeForm" method="POST" action="empleados.php" class="form-container">
                <label for="rut">RUT:</label>
                <input type="text" id="rut" name="rut" class="input-field" required pattern="^\d{1,2}\.\d{3}\.\d{3}-[0-9Kk]$" title="Formato: 12.123.123-4" oninput="formatRUT(this)">
                <small>Formato: 12.123.123-4</small>
                <br><br>

                <label for="tipo_empleado">Tipo de Empleado:</label>
                <select id="tipo_empleado" name="tipo_empleado" class="select-field" required>
                    <option value="">Seleccione</option>
                    <option value="Administrador">Administrador</option>
                    <option value="Vendedor">Vendedor</option>
                </select>
                <br><br>

                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" class="input-field" required>
                <br><br>

                <label for="apellido">Apellido:</label>
                <input type="text" id="apellido" name="apellido" class="input-field" required>
                <br><br>

                <label for="direccion_empleado">Dirección:</label>
                <input type="text" id="direccion_empleado" name="direccion_empleado" class="input-field">
                <br><br>

                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" class="input-field">
                <br><br>

                <label for="email">Correo Electrónico:</label>
                <input type="email" id="email" name="email" class="input-field">
                <br><br>

                <label for="local_id">Local:</label>
                <select id="local_id" name="local_id" class="select-field" required>
                    <option value="">Seleccione un local</option>
                    <?php foreach ($locales as $local): ?>
                        <option value="<?= $local['id_local']; ?>"><?= htmlspecialchars($local['nombre']); ?></option>
                    <?php endforeach; ?>
                </select>
                <br><br>

                <button type="submit" class="add-employee-button">Agregar Empleado</button>
            </form>
        </div>



        <script src="js/usuarios.js"></script>
</body>