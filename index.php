<?php
// Incluimos la conexión a la base de datos
require_once 'conexion.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Prueba de Conexión - Control de Accesos</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; background-color: #f4f4f9; text-align: center; }
        .card { background: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); display: inline-block; max-width: 500px; }
        h2 { color: #333; }
        .success { color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 12px; border-radius: 4px; font-weight: bold; margin-top: 20px; }
    </style>
</head>
<body>

    <div class="card">
        <h2>Sistema de Control de Accesos</h2>
        <p>Verificando el estado de la capa de datos...</p>
        
        <?php if ($pdo): ?>
            <div class="success">
                ¡Conexión exitosa a la base de datos `control_accesos` usando PDO!
            </div>
        <?php endif; ?>
    </div>

</body>
</html>