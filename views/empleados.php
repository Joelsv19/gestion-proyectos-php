<?php
$empleadoEdicion = null;
if (isset($_GET['editar'])) {
    $idEdicion = filter_var($_GET['editar'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    if ($idEdicion) {
        $empleadoEdicion = $empleado->buscar((int)$idEdicion);
    }
}
?>
<section>
    <h2><?= $empleadoEdicion ? 'Editar empleado' : 'Registrar empleado' ?></h2>
    <?php if (isset($_GET['editar']) && !$empleadoEdicion): ?>
        <p class="mensaje error">No se encontró el empleado solicitado.</p>
    <?php endif; ?>
    <form method="post" action="index.php?seccion=empleados" class="formulario">
        <input type="hidden" name="accion" value="<?= $empleadoEdicion ? 'editar' : 'crear' ?>">
        <?php if ($empleadoEdicion): ?>
            <input type="hidden" name="empleado_id" value="<?= e($empleadoEdicion['empleado_id']) ?>">
        <?php endif; ?>
        <label for="nombre">Nombre</label>
        <input id="nombre" name="nombre" type="text" maxlength="80" required value="<?= e($empleadoEdicion['nombre'] ?? '') ?>">

        <label for="apellido">Apellido</label>
        <input id="apellido" name="apellido" type="text" maxlength="80" required value="<?= e($empleadoEdicion['apellido'] ?? '') ?>">

        <label for="email">Correo electrónico</label>
        <input id="email" name="email" type="email" maxlength="150" required value="<?= e($empleadoEdicion['email'] ?? '') ?>">

        <label for="posicion">Posición</label>
        <input id="posicion" name="posicion" type="text" maxlength="100" required value="<?= e($empleadoEdicion['posicion'] ?? '') ?>">

        <div class="acciones">
            <button type="submit"><?= $empleadoEdicion ? 'Guardar cambios' : 'Guardar' ?></button>
            <?php if ($empleadoEdicion): ?>
                <a class="boton secundario" href="index.php?seccion=empleados">Cancelar</a>
            <?php endif; ?>
        </div>
    </form>
</section>

<section>
    <h2>Empleados registrados</h2>
    <div class="tabla-contenedor">
        <table>
            <thead>
                <tr><th>Nombre</th><th>Apellido</th><th>Correo</th><th>Posición</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                <?php if (!$listaEmpleados): ?>
                    <tr><td colspan="5">No hay empleados registrados.</td></tr>
                <?php else: ?>
                    <?php foreach ($listaEmpleados as $fila): ?>
                        <tr>
                            <td><?= e($fila['nombre']) ?></td>
                            <td><?= e($fila['apellido']) ?></td>
                            <td><?= e($fila['email']) ?></td>
                            <td><?= e($fila['posicion']) ?></td>
                            <td class="acciones-tabla">
                                <a class="boton pequeno" href="index.php?seccion=empleados&amp;editar=<?= e($fila['empleado_id']) ?>">Editar</a>
                                <form method="post" action="index.php?seccion=empleados" onsubmit="return confirm('¿Eliminar este empleado y sus asignaciones?');">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="empleado_id" value="<?= e($fila['empleado_id']) ?>">
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
