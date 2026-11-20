<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Proyectos</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<header class="encabezado">
    <h1>Sistema de Gestión de Proyectos</h1>
    <nav aria-label="Menú principal">
        <a href="index.php?seccion=proyectos" <?= $seccion === 'proyectos' ? 'aria-current="page"' : '' ?>>Proyectos</a>
        <a href="index.php?seccion=empleados" <?= $seccion === 'empleados' ? 'aria-current="page"' : '' ?>>Empleados</a>
        <a href="index.php?seccion=asignaciones" <?= $seccion === 'asignaciones' ? 'aria-current="page"' : '' ?>>Asignaciones</a>
    </nav>
</header>
<main class="contenedor">
    <?php if ($mensaje): ?>
        <p class="mensaje <?= e($mensaje['tipo']) ?>" role="status"><?= e($mensaje['mensaje']) ?></p>
    <?php endif; ?>
