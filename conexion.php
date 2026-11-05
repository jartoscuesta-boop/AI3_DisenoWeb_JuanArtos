<?php
// conexion.php - Configuración de la conexión segura con PDO

$host = 'localhost';
$db   = 'control_accesos';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false, // Desactiva la emulación para mayor seguridad estricta
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // En un entorno de producción se recomienda registrar el error en un log privado
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>