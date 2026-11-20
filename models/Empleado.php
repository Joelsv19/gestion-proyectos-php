<?php
class Empleado
{
    private mysqli $conexion;

    public function __construct(mysqli $conexion)
    {
        $this->conexion = $conexion;
    }

    public function listar(): array
    {
        $consulta = $this->conexion->query(
            'SELECT empleado_id, nombre, apellido, email, posicion
             FROM Empleados ORDER BY empleado_id DESC'
        );
        return $consulta->fetch_all(MYSQLI_ASSOC);
    }

    public function buscar(int $empleadoId): ?array
    {
        $consulta = $this->conexion->prepare(
            'SELECT empleado_id, nombre, apellido, email, posicion
             FROM Empleados WHERE empleado_id = ?'
        );
        $consulta->bind_param('i', $empleadoId);
        $consulta->execute();
        $empleado = $consulta->get_result()->fetch_assoc();
        return $empleado ?: null;
    }

    public function existe(int $empleadoId): bool
    {
        return $this->buscar($empleadoId) !== null;
    }

    public function emailExiste(string $email, ?int $excluirId = null): bool
    {
        if ($excluirId === null) {
            $consulta = $this->conexion->prepare(
                'SELECT empleado_id FROM Empleados WHERE email = ? LIMIT 1'
            );
            $consulta->bind_param('s', $email);
        } else {
            $consulta = $this->conexion->prepare(
                'SELECT empleado_id FROM Empleados WHERE email = ? AND empleado_id <> ? LIMIT 1'
            );
            $consulta->bind_param('si', $email, $excluirId);
        }
        $consulta->execute();
        return $consulta->get_result()->num_rows > 0;
    }

    public function guardar(string $nombre, string $apellido, string $email, string $posicion): void
    {
        $consulta = $this->conexion->prepare(
            'INSERT INTO Empleados (nombre, apellido, email, posicion) VALUES (?, ?, ?, ?)'
        );
        $consulta->bind_param('ssss', $nombre, $apellido, $email, $posicion);
        $consulta->execute();
    }

    public function actualizar(int $empleadoId, string $nombre, string $apellido, string $email, string $posicion): bool
    {
        $consulta = $this->conexion->prepare(
            'UPDATE Empleados SET nombre = ?, apellido = ?, email = ?, posicion = ?
             WHERE empleado_id = ?'
        );
        $consulta->bind_param('ssssi', $nombre, $apellido, $email, $posicion, $empleadoId);
        $consulta->execute();
        return $consulta->affected_rows > 0 || $this->buscar($empleadoId) !== null;
    }

    public function eliminar(int $empleadoId): bool
    {
        $consulta = $this->conexion->prepare('DELETE FROM Empleados WHERE empleado_id = ?');
        $consulta->bind_param('i', $empleadoId);
        $consulta->execute();
        return $consulta->affected_rows > 0;
    }
}
