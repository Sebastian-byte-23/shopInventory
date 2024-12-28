<?php
$host = 'localhost';       // Cambia esto si tu base de datos está en otro servidor
$db = 'inventario';        // Nombre de tu base de datos
$user = 'root';            // Usuario de la base de datos
$pass = '';                // Contraseña de la base de datos

try {
    // Configuración de DSN con el conjunto de caracteres utf8mb4
    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

    // Opciones para optimizar la seguridad y el rendimiento de la conexión
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,            // Activar manejo de errores con excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Establecer el modo de obtención predeterminado
        PDO::ATTR_EMULATE_PREPARES => false,                    // Desactivar emulación de consultas preparadas
        PDO::ATTR_PERSISTENT => true,                           // Habilitar conexiones persistentes (opcional)
    ];

    // Crear una nueva instancia de PDO con opciones
    $pdo = new PDO($dsn, $user, $pass, $options);

} catch (PDOException $e) {
    // Ocultar detalles del error en producción y mostrar un mensaje genérico
    error_log("Error de conexión a la base de datos: " . $e->getMessage()); // Registro de errores
    exit("Ocurrió un problema al conectar con la base de datos. Inténtelo más tarde."); // Mensaje seguro para el usuario
}
?>
