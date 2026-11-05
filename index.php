<?php
// Incluir la conexión segura a la base de datos
require_once 'conexion.php';

$mensaje = "";
$tipo_alerta = "";

// 2. Procesamiento y validación del lado del servidor
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitización básica con trim
    $nombre  = trim($_POST['nombre_completo'] ?? '');
    $cedula  = trim($_POST['cedula'] ?? '');
    $motivo  = trim($_POST['motivo_visita'] ?? '');
    $destino = trim($_POST['persona_a_visitar'] ?? '');

    // Validaciones estrictas en el servidor
    if (empty($nombre) || empty($cedula) || empty($motivo) || empty($destino)) {
        $mensaje = "Error: Todos los campos son obligatorios.";
        $tipo_alerta = "error";
    } elseif (strlen($cedula) < 10) {
        $mensaje = "Error: El número de cédula debe tener al menos 10 dígitos.";
        $tipo_alerta = "error";
    } else {
        try {
            // 3. SENTENCIA PREPARADA (Prepared Statement) contra Inyección SQL
            $sql = "INSERT INTO visitantes (nombre_completo, cedula, motivo_visita, persona_a_visitar) 
                    VALUES (:nombre, :cedula, :motivo, :destino)";
            
            $stmt = $pdo->prepare($sql);
            
            // Ejecución segura enlazando los parámetros con nombre
            $stmt->execute([
                'nombre'  => $nombre,
                'cedula'  => $cedula,
                'motivo'  => $motivo,
                'destino' => $destino
            ]);

            $mensaje = "¡Éxito! Visita registrada e inserción completada correctamente.";
            $tipo_alerta = "success";
        } catch (\PDOException $e) {
            $mensaje = "Error en la base de datos: " . $e->getMessage();
            $tipo_alerta = "error";
        }
    }
}

// Consultar todos los registros de la tabla para mostrarlos en la interfaz web
$stmtQuery = $pdo->query("SELECT * FROM visitantes ORDER BY id DESC");
$visitantes = $stmtQuery->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control de Accesos - Registro de Visitantes</title>
    <!-- Enlace a la hoja de estilos externa -->
    <link rel="stylesheet" href="style/style.css">
    <style>
        /* Ajustes adicionales rápidos para alertas de éxito/error */
        .msg.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .msg.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>

<div class="container">
    <h2>Sistema de Control de Accesos</h2>
    <p>Módulo Web Full-Stack - Registro y Gestión de Visitantes</p>

    <!-- Mensaje de respuesta del servidor -->
    <?php if (!empty($mensaje)): ?>
        <div class="msg <?= $tipo_alerta ?>">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <!-- 1. Formulario con validación en el cliente (HTML5) -->
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
            <input type="text" id="persona_a_visitar" name="persona_a_visitar" required maxlength="100" placeholder="Ej. Ing. Informática / Gerencia">
        </div>

        <button type="submit">Registrar Ingreso</button>
    </form>

    <hr style="border: 0; border-top: 1px solid #eee; margin: 30px 0;">

    <!-- Tabla web mostrando los registros de la base de datos -->
    <h3>Historial de Visitantes Registrados</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre Completo</th>
                <th>Cédula</th>
                <th>Motivo</th>
                <th>Destino</th>
                <th>Hora de Ingreso</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($visitantes) > 0): ?>
                <?php foreach ($visitantes as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($row['cedula']) ?></td>
                        <td><?= htmlspecialchars($row['motivo_visita']) ?></td>
                        <td><?= htmlspecialchars($row['persona_a_visitar']) ?></td>
                        <td><?= htmlspecialchars($row['hora_ingreso']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align: center; color: #777;">No hay registros de visitantes en la base de datos.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>