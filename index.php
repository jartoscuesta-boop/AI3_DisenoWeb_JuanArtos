<?php
// conexion.php maneja la conexión segura con PDO
require_once 'conexion.php';

$mensaje = "";
$tipo_alerta = "";

// Procesamiento de Inserción (Create) via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre  = trim($_POST['nombre_completo'] ?? '');
    $cedula  = trim($_POST['cedula'] ?? '');
    $motivo  = trim($_POST['motivo_visita'] ?? '');
    $destino = trim($_POST['persona_a_visitar'] ?? '');

    // Validación básica en el servidor
    if (empty($nombre) || empty($cedula) || empty($motivo) || empty($destino)) {
        $mensaje = "Error: Todos los campos son obligatorios.";
        $tipo_alerta = "error";
    } elseif (strlen($cedula) < 10) {
        $mensaje = "Error: El número de cédula debe tener al menos 10 dígitos.";
        $tipo_alerta = "error";
    } else {
        try {
            // SENTENCIA PREPARADA contra Inyección SQL
            $sql = "INSERT INTO visitantes (nombre_completo, cedula, motivo_visita, persona_a_visitar) 
                    VALUES (:nombre, :cedula, :motivo, :destino)";
            
            $stmt = $pdo->prepare($sql);
            
            // Ejecución segura enlazando los parámetros
            $stmt->execute([
                'nombre'  => $nombre,
                'cedula'  => $cedula,
                'motivo'  => $motivo,
                'destino' => $destino
            ]);

            $mensaje = "¡Éxito! Ingreso de visitante registrado correctamente.";
            $tipo_alerta = "success";
        } catch (\PDOException $e) {
            $mensaje = "Error en la base de datos: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control de Accesos - Registro de Visitantes</title>
    <link rel="stylesheet" href="style/style.css">
    <style>
        .msg.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .msg.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .nav-links { margin-bottom: 20px; text-align: right; }
        .nav-links a { background: #6c757d; color: white; padding: 8px 12px; border-radius: 4px; text-decoration: none; font-size: 14px; }
        .nav-links a:hover { background: #5a6268; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-links">
        <a href="consulta.php">Ir al Módulo de Consulta &rarr;</a>
    </div>

    <h2>Sistema de Control de Accesos</h2>
    <p>Módulo de Registro de Nuevos Ingresos</p>

    <!-- Alertas de servidor -->
    <?php if (!empty($mensaje)): ?>
        <div class="msg <?= $tipo_alerta ?>">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <!-- Formulario de Registro (Create) con validación HTML5 -->
    <form method="POST" action="">
        <div>
            <label for="nombre_completo">Nombre Completo:</label>
            <input type="text" id="nombre_completo" name="nombre_completo" required maxlength="100" placeholder="Ej. Carlos Mendoza">
        </div>
        
        <div>
            <label for="cedula">Cédula de Identidad:</label>
            <input type="text" id="cedula" name="cedula" required maxlength="20" pattern="[0-9]+" title="Ingrese solo números" placeholder="Ej. 1712345678">
        </div>

        <div>
            <label for="motivo_visita">Motivo de la Visita:</label>
            <textarea id="motivo_visita" name="motivo_visita" rows="3" required placeholder="Detalle el motivo de la visita..."></textarea>
        </div>

        <div>
            <label for="persona_a_visitar">Persona o Departamento a Visitar:</label>
            <input type="text" id="persona_a_visitar" name="persona_a_visitar" required maxlength="100" placeholder="Ej. Gerencia / Ing. Informática">
        </div>

        <button type="submit">Registrar Ingreso</button>
    </form>
</div>

</body>
</html>