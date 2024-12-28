<?php
session_start(); // Iniciar la sesión al comienzo del archivo
ob_start(); // Iniciar el almacenamiento en búfer de salida

include 'login/conexion.php';

$error = ''; // Variable para almacenar el mensaje de error

if (isset($_POST['bt2'])) {
    try {
        // Tomar datos del formulario de manera segura
        $user = trim($_POST['usuario']);
        $pass = $_POST['contrasena'];

        // Preparar la consulta para verificar si el usuario existe
        $sql = "SELECT * FROM usuarios WHERE user = :user";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':user', $user, PDO::PARAM_STR);
        $stmt->execute();

        // Verificar si se encontró el usuario
        if ($stmt->rowCount() > 0) {
            $fila = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verificar la contraseña
            if (password_verify($pass, $fila['pass'])) {
                $_SESSION['user'] = $user; // Guardar el nombre de usuario en la sesión

                // Redirigir según el tipo_empleado
                if ($fila['tipo_empleado'] == 'Administrador') {
                    header("Location: admin/menu.php");
                } else if ($fila['tipo_empleado'] == 'Vendedor') {
                    header("Location: vendedor/menu_vende.php");
                }
                exit();
            } else {
                $error = 'Usuario y/o Contraseña incorrecta';
            }
        } else {
            $error = 'Usuario no encontrado';
        }
    } catch (PDOException $e) {
        error_log("Error en la consulta de inicio de sesión: " . $e->getMessage()); // Registrar el error
        $error = 'Ocurrió un problema al procesar su solicitud. Inténtelo más tarde.'; // Mensaje seguro para el usuario
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="./login/css/login.css"> <!-- Archivo CSS externo -->
</head>
<body>

<section class="form-login">
    <h4>INICIO DE SESIÓN</h4>
    <br><br>

    <form action="" method="post">
        <label for="usuario">Usuario</label>
        <input class="controls" type="text" name="usuario" id="usuario" placeholder="Ingrese su nombre de usuario" required>

        <label for="contrasena">Contraseña</label>
        <input class="controls" type="password" name="contrasena" id="contrasena" placeholder="Ingrese su contraseña" required>

        <!-- Mostrar mensaje de error si existe -->
        <?php if (!empty($error)): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <input class="buttons" type="submit" name="bt2" value="Ingresar">
    </form>
</section>

</body>
</html>
