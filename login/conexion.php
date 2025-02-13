<?php
$servidor = 'localhost';
$usuario = 'root';
$clave = '';
$bd = 'inventario';

try {
    // Configuración de DSN para PDO
    $dsn = "mysql:host=$servidor;dbname=$bd;charset=utf8mb4";

    // Opciones para mejorar la seguridad
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,    // Manejo de errores con excepciones
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Modo de obtención predeterminado como array asociativo
        PDO::ATTR_EMULATE_PREPARES => false,             // Desactivar emulación de consultas preparadas
    ];

    // Conexión segura utilizando PDO
    $pdo = new PDO($dsn, $usuario, $clave, $options);

} catch (PDOException $e) {
    // Manejo de errores seguro
    die("Error de conexión a la base de datos: " . $e->getMessage());
}

?>
