<section>
    <h2>Asignar empleados a un proyecto</h2>
    <?php if (!$listaProyectos || !$listaEmpleados): ?>
        <p>Registre al menos un proyecto y un empleado antes de crear asignaciones.</p>
    <?php else: ?>
        <form method="post" action="index.php?seccion=asignaciones" class="formulario">
            <input type="hidden" name="accion" value="asignar">
            <label for="proyecto_id">Proyecto</label>
            <select id="proyecto_id" name="proyecto_id" required>
                <option value="">Seleccione un proyecto</option>
                <?php foreach ($listaProyectos as $fila): ?>
                    <option value="<?= e($fila['proyecto_id']) ?>"><?= e($fila['nombre']) ?></option>
                <?php endforeach; ?>
            </select>

            <label for="empleados">Empleados (puede seleccionar varios)</label>
            <select id="empleados" name="empleados[]" multiple size="5" required>
                <?php foreach ($listaEmpleados as $fila): ?>
                    <option value="<?= e($fila['empleado_id']) ?>"><?= e($fila['nombre'] . ' ' . $fila['apellido'] . ' — ' . $fila['email']) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="acciones"><button type="submit">Guardar asignaciones</button></div>
        </form>
    <?php endif; ?>
</section>

<section>
    <h2>Empleados asignados</h2>
    <div class="tabla-contenedor">
        <table>
            <thead>
                <tr><th>Proyecto</th><th>Empleado</th><th>Correo</th><th>Acción</th></tr>
            </thead>
            <tbody>
                <?php if (!$listaAsignaciones): ?>
                    <tr><td colspan="4">No hay asignaciones registradas.</td></tr>
                <?php else: ?>
                    <?php foreach ($listaAsignaciones as $fila): ?>
                        <tr>
                            <td><?= e($fila['proyecto']) ?></td>
                            <td><?= e($fila['nombre'] . ' ' . $fila['apellido']) ?></td>
                            <td><?= e($fila['email']) ?></td>
                            <td>
                                <form method="post" action="index.php?seccion=asignaciones" onsubmit="return confirm('¿Retirar este empleado del proyecto?');">
                                    <input type="hidden" name="accion" value="eliminar">
                                    <input type="hidden" name="asignacion_id" value="<?= e($fila['asignacion_id']) ?>">
                                    <button class="peligro pequeno" type="submit">Quitar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>
