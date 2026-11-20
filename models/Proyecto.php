<?php
class Proyecto
{
    private mysqli $conexion;

    public function __construct(mysqli $conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar(): array
    {
        $consulta = "SELECT p.proyecto_id, p.nombre, p.descripcion, p.fecha_inicio, p.fecha_fin,
                            GROUP_CONCAT(CONCAT(e.nombre, ' ', e.apellido)
                                ORDER BY e.apellido, e.nombre SEPARATOR ', ') AS empleados
                     FROM Proyectos p
                     LEFT JOIN Asignaciones a ON a.proyecto_id = p.proyecto_id
                     LEFT JOIN Empleados e ON e.empleado_id = a.empleado_id
                     GROUP BY p.proyecto_id, p.nombre, p.descripcion, p.fecha_inicio, p.fecha_fin
                     ORDER BY p.proyecto_id DESC";
        return $this->conexion->query($consulta)->fetch_all(MYSQLI_ASSOC);
    }

    public function buscar(int $proyectoId): ?array
    {
        $consulta = $this->conexion->prepare(
            'SELECT proyecto_id, nombre, descripcion, fecha_inicio, fecha_fin
             FROM Proyectos WHERE proyecto_id = ?'
        );
        $consulta->bind_param('i', $proyectoId);
        $consulta->execute();
        $proyecto = $consulta->get_result()->fetch_assoc();
        return $proyecto ?: null;
    }

    public function nombreExiste(string $nombre, ?int $excluirId = null): bool
    {
        if ($excluirId === null) {
            $consulta = $this->conexion->prepare(
                'SELECT proyecto_id FROM Proyectos WHERE nombre = ? LIMIT 1'
            );
            $consulta->bind_param('s', $nombre);
        } else {
            $consulta = $this->conexion->prepare(
                'SELECT proyecto_id FROM Proyectos WHERE nombre = ? AND proyecto_id <> ? LIMIT 1'
            );
            $consulta->bind_param('si', $nombre, $excluirId);
        }
        $consulta->execute();
        return $consulta->get_result()->num_rows > 0;
    }

    public function guardar(string $nombre, string $descripcion, string $fechaInicio, string $fechaFin): void
    {
        $consulta = $this->conexion->prepare(
            'INSERT INTO Proyectos (nombre, descripcion, fecha_inicio, fecha_fin) VALUES (?, ?, ?, ?)'
        );
        $consulta->bind_param('ssss', $nombre, $descripcion, $fechaInicio, $fechaFin);
        $consulta->execute();
    }

    public function actualizar(int $proyectoId, string $nombre, string $descripcion, string $fechaInicio, string $fechaFin): bool
    {
        $consulta = $this->conexion->prepare(
            'UPDATE Proyectos SET nombre = ?, descripcion = ?, fecha_inicio = ?, fecha_fin = ?
             WHERE proyecto_id = ?'
        );
        $consulta->bind_param('ssssi', $nombre, $descripcion, $fechaInicio, $fechaFin, $proyectoId);
        $consulta->execute();
        return $consulta->affected_rows > 0 || $this->buscar($proyectoId) !== null;
    }

    public function eliminar(int $proyectoId): bool
    {
        $consulta = $this->conexion->prepare('DELETE FROM Proyectos WHERE proyecto_id = ?');
        $consulta->bind_param('i', $proyectoId);
        $consulta->execute();
        return $consulta->affected_rows > 0;
    }
}
