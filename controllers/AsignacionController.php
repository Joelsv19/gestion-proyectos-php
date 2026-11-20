<?php
class AsignacionController
{
    private Asignacion $asignacion;
    private Proyecto $proyecto;
    private Empleado $empleado;

    public function __construct(Asignacion $asignacion, Proyecto $proyecto, Empleado $empleado)
    {
        $this->asignacion = $asignacion;
        $this->proyecto = $proyecto;
        $this->empleado = $empleado;
    }

    public function procesar(string $accion, array $datos): array
    {
        if ($accion === 'eliminar') {
            $id = filter_var($datos['asignacion_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if (!$id || !$this->asignacion->eliminar((int)$id)) {
                return ['tipo' => 'error', 'mensaje' => 'No se encontró la asignación.'];
            }
            return ['tipo' => 'exito', 'mensaje' => 'Empleado retirado del proyecto.'];
        }

        $proyectoId = filter_var($datos['proyecto_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $empleadoIds = $datos['empleados'] ?? [];
        if (!$proyectoId || !$this->proyecto->buscar((int)$proyectoId)) {
            return ['tipo' => 'error', 'mensaje' => 'Seleccione un proyecto válido.'];
        }
        if (!is_array($empleadoIds) || count($empleadoIds) === 0) {
            return ['tipo' => 'error', 'mensaje' => 'Seleccione uno o varios empleados.'];
        }

        $ids = [];
        foreach ($empleadoIds as $empleadoId) {
            $id = filter_var($empleadoId, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            if (!$id || !$this->empleado->existe((int)$id)) {
                return ['tipo' => 'error', 'mensaje' => 'La selección contiene un empleado no válido.'];
            }
            $ids[] = (int)$id;
        }

        try {
            $resultado = $this->asignacion->agregarVarios((int)$proyectoId, array_values(array_unique($ids)));
            if ($resultado['agregadas'] === 0 && $resultado['omitidas'] > 0) {
                return ['tipo' => 'exito', 'mensaje' => 'Las asignaciones ya existían; no se duplicaron.'];
            }
            $mensaje = 'Se guardaron ' . $resultado['agregadas'] . ' asignación(es).';
            if ($resultado['omitidas'] > 0) {
                $mensaje .= ' ' . $resultado['omitidas'] . ' ya existían y no se duplicaron.';
            }
            return ['tipo' => 'exito', 'mensaje' => $mensaje];
        } catch (mysqli_sql_exception $error) {
            return ['tipo' => 'error', 'mensaje' => 'No se pudieron guardar las asignaciones. Revise la selección e intente nuevamente.'];
        }
    }
}
