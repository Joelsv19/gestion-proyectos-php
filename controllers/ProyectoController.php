<?php
class ProyectoController
{
    private Proyecto $proyecto;

    public function __construct(Proyecto $proyecto)
    {
        $this->proyecto = $proyecto;
    }

    public function procesar(string $accion, int $id, array $datos): array
    {
        if ($accion === 'eliminar') {
            return $this->proyecto->eliminar($id)
                ? ['tipo' => 'exito', 'mensaje' => 'Proyecto eliminado. Sus asignaciones también fueron retiradas.']
                : ['tipo' => 'error', 'mensaje' => 'No se encontró el proyecto.'];
        }

        $nombre = trim($datos['nombre'] ?? '');
        $descripcion = trim($datos['descripcion'] ?? '');
        $fechaInicio = trim($datos['fecha_inicio'] ?? '');
        $fechaFin = trim($datos['fecha_fin'] ?? '');

        if ($nombre === '' || $descripcion === '' || $fechaInicio === '' || $fechaFin === '') {
            return ['tipo' => 'error', 'mensaje' => 'Complete todos los campos del proyecto.'];
        }
        if (mb_strlen($nombre) > 100) {
            return ['tipo' => 'error', 'mensaje' => 'El nombre no puede superar 100 caracteres.'];
        }
        if (!$this->fechaValida($fechaInicio) || !$this->fechaValida($fechaFin)) {
            return ['tipo' => 'error', 'mensaje' => 'Ingrese fechas válidas.'];
        }
        if ($fechaFin < $fechaInicio) {
            return ['tipo' => 'error', 'mensaje' => 'La fecha de finalización no puede ser anterior a la fecha de inicio.'];
        }
        if ($this->proyecto->nombreExiste($nombre, $accion === 'editar' ? $id : null)) {
            return ['tipo' => 'error', 'mensaje' => 'Ya existe un proyecto con ese nombre.'];
        }

        try {
            if ($accion === 'editar') {
                if (!$this->proyecto->actualizar($id, $nombre, $descripcion, $fechaInicio, $fechaFin)) {
                    return ['tipo' => 'error', 'mensaje' => 'No se encontró el proyecto.'];
                }
                return ['tipo' => 'exito', 'mensaje' => 'Proyecto actualizado.'];
            }

            $this->proyecto->guardar($nombre, $descripcion, $fechaInicio, $fechaFin);
            return ['tipo' => 'exito', 'mensaje' => 'Proyecto registrado.'];
        } catch (mysqli_sql_exception $error) {
            return ['tipo' => 'error', 'mensaje' => $error->getCode() === 1062
                ? 'Ya existe un proyecto con ese nombre.'
                : 'No se pudo guardar el proyecto. Revise los datos e intente nuevamente.'];
        }
    }

    private function fechaValida(string $fecha): bool
    {
        $valor = DateTime::createFromFormat('!Y-m-d', $fecha);
        $errores = DateTime::getLastErrors();
        return $valor !== false
            && $valor->format('Y-m-d') === $fecha
            && ($errores === false || ($errores['warning_count'] === 0 && $errores['error_count'] === 0));
    }
}
