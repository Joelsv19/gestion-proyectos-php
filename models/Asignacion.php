<?php
class Asignacion
{
    private mysqli $conexion;

    public function __construct(mysqli $conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar(): array
    {
        $consulta = $this->conexion->query(
            'SELECT a.asignacion_id, p.proyecto_id, p.nombre AS proyecto,
                    e.empleado_id, e.nombre, e.apellido, e.email
             FROM Asignaciones a
             INNER JOIN Proyectos p ON p.proyecto_id = a.proyecto_id
             INNER JOIN Empleados e ON e.empleado_id = a.empleado_id
             ORDER BY p.nombre, e.apellido, e.nombre'
        );
        return $consulta->fetch_all(MYSQLI_ASSOC);
    }

    public function agregarVarios(int $proyectoId, array $empleadoIds): array
    {
        $agregadas = 0;
        $omitidas = 0;
        $this->conexion->begin_transaction();

        try {
            $buscar = $this->conexion->prepare(
                'SELECT asignacion_id FROM Asignaciones WHERE proyecto_id = ? AND empleado_id = ?'
            );
            $insertar = $this->conexion->prepare(
                'INSERT INTO Asignaciones (proyecto_id, empleado_id) VALUES (?, ?)'
            );

            foreach ($empleadoIds as $empleadoId) {
                $buscar->bind_param('ii', $proyectoId, $empleadoId);
                $buscar->execute();
                if ($buscar->get_result()->num_rows > 0) {
                    $omitidas++;
                    continue;
                }

                $insertar->bind_param('ii', $proyectoId, $empleadoId);
                $insertar->execute();
                $agregadas++;
            }

            $this->conexion->commit();
            return ['agregadas' => $agregadas, 'omitidas' => $omitidas];
        } catch (Throwable $error) {
            $this->conexion->rollback();
            throw $error;
        }
    }

    public function eliminar(int $asignacionId): bool
    {
        $consulta = $this->conexion->prepare('DELETE FROM Asignaciones WHERE asignacion_id = ?');
        $consulta->bind_param('i', $asignacionId);
        $consulta->execute();
        return $consulta->affected_rows > 0;
    }
}
