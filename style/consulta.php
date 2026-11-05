<?php
require_once 'conexion.php';

$busqueda = trim($_GET['busqueda'] ?? '');
$mensaje = "";
$tipo_alerta = "";

try {
    if (!empty($busqueda)) {
        // Consulta filtrada utilizando Sentencia Preparada contra Inyección SQL
        $sqlQuery = "SELECT * FROM visitantes WHERE nombre_completo LIKE :busq OR cedula LIKE :busq ORDER BY id DESC";
        $stmtQuery = $pdo->prepare($sqlQuery);
        $stmtQuery->execute(['busq' => "%$busqueda%"]);
    } else {
        // Consulta general de todos los registros
        $stmtQuery = $pdo->query("SELECT * FROM visitantes ORDER BY id DESC");
    }
    $visitantes = $stmtQuery->fetchAll();
} catch (\PDOException $e) {
    $visitantes = [];
    $mensaje = "Error al consultar los registros: " . $e->getMessage();
    $tipo_alerta = "error";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control de Accesos - Módulo de Consulta</title>
    <link rel="stylesheet" href="style/style.css">
    <style>
        .nav-links { margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .nav-links a { background: #007bff; color: white; padding: 8px 12px; border-radius: 4px; text-decoration: none; font-size: 14px; }
        .nav-links a:hover { background: #0056b3; }
        .search-box { display: flex; gap: 10px; margin-bottom: 20px; }
        .search-box input { flex: 1; }
        .btn-search { background: #28a745; }
        .btn-search:hover { background: #218838; }
        .btn-reset { background: #6c757d; text-decoration: none; padding: 10px 15px; color: white; border-radius: 4px; display: inline-block; font-size: 14px; }
        .btn-reset:hover { background: #5a6268; }
        .msg.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="nav-links">
        <a href="index.php">&larr; Volver al Registro de Visitantes</a>
        <span style="font-weight: bold; color: #555;">Módulo Independiente de Consulta</span>
    </div>

    <h2>Módulo de Consulta de Registros</h2>
    <p>Búsqueda y filtrado seguro de visitantes almacenados en la base de datos</p>

    <?php if (!empty($mensaje)): ?>
        <div class="msg error"><?= htmlspecialchars($mensaje) ?></div>
    <?php endif; ?>

    <!-- Formulario de Búsqueda -->
    <form method="GET" action="" class="search-box">
        <input type="text" name="busqueda" value="<?= htmlspecialchars($busqueda) ?>" placeholder="Buscar por Nombre o Cédula...">
        <button type="submit" class="btn-search">Buscar</button>
        <?php if (!empty($busqueda)): ?>
            <a href="consulta.php" class="btn-reset">Limpiar Filtro</a>
        <?php endif; ?>
    </form>

    <!-- Tabla Responsiva y Saneamiento XSS -->
    <div class="table-responsive">
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
                        <td colspan="6" style="text-align: center; color: #777;">No se encontraron registros de visitantes.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>