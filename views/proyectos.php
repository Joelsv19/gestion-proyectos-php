<?php
$proyectoEdicion = null;
if (isset($_GET['editar'])) {
    $idEdicion = filter_var($_GET['editar'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($idEdicion) {
        $proyectoEdicion = $proyecto->buscar((int)$idEdicion);
    }
}
?>
<section>
    <h2><?= $proyectoEdicion ? 'Editar proyecto' : 'Registrar proyecto' ?></h2>
    <?php if (isset($_GET['editar']) && !$proyectoEdicion): ?>
        <p class="mensaje error">No se encontró el proyecto solicitado.</p>
    <?php endif; ?>
    <form method="post" action="index.php?seccion=proyectos" class="formulario">
        <input type="hidden" name="accion" value="<?= $proyectoEdicion ? 'editar' : 'crear' ?>">
        <?php if ($proyectoEdicion): ?>
            <input type="hidden" name="proyecto_id" value="<?= e($proyectoEdicion['proyecto_id']) ?>">
        <?php endif; ?>
        <label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" type="text" maxlength="100" required value="<?= e($proyectoEdicion['nombre'] ?? '') ?>">

        <label for="descripcion">Descripción</label>
        <textarea id="descripcion" name="descripcion" rows="3" required><?= e($proyectoEdicion['descripcion'] ?? '') ?></textarea>

        <div class="fila-fechas">
            <div>
                <label for="fecha_inicio">Fecha de inicio</label>
                <input id="fecha_inicio" name="fecha_inicio" type="date" required value="<?= e($proyectoEdicion['fecha_inicio'] ?? '') ?>">
            </div>
            <div>
                <label for="fecha_fin">Fecha de finalización</label>
                <input id="fecha_fin" name="fecha_fin" type="date" required value="<?= e($proyectoEdicion['fecha_fin'] ?? '') ?>">
            </div>
        </div>

        <div class="acciones">
            <button type="submit"><?= $proyectoEdicion ? 'Guardar cambios' : 'Guardar' ?></button>
            <?php if ($proyectoEdicion): ?>
                <a class="boton secundario" href="index.php?seccion=proyectos">Cancelar</a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section>
    <h2>Proyectos registrados</h2>
    <div class="tabla-contenedor">
        <table>
            <thead>
                <tr><th>Nombre</th><th>Descripción</th><th>Inicio</th><th>Fin</th><th>Empleados asignados</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                <?php if (!$listaProyectos): ?>
                    <tr><td colspan="6">No hay proyectos registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($listaProyectos as $fila): ?>
                        <tr>
                            <td><?= e($fila['nombre']) ?></td>
                            <td><?= e($fila['descripcion']) ?></td>
                            <td><?= e($fila['fecha_inicio']) ?></td>
                            <td><?= e($fila['fecha_fin']) ?></td>
                            <td><?= e($fila['empleados'] ?: 'Sin empleados') ?></td>
                            <td class="acciones-tabla">
                                <a class="boton pequeno" href="index.php?seccion=proyectos&amp;editar=<?= e($fila['proyecto_id']) ?>">Editar</a>
                                <form method="post" action="index.php?seccion=proyectos" onsubmit="return confirm('¿Eliminar este proyecto y sus asignaciones?');">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="proyecto_id" value="<?= e($fila['proyecto_id']) ?>">
                                    <button class="peligro pequeno" type="submit">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
