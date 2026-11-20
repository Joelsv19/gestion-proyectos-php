<?php
class EmpleadoController
{
    private Empleado $empleado;

    public function __construct(Empleado $empleado)
    {
        $this->empleado = $empleado;
    }

    public function procesar(string $accion, int $id, array $datos): array
    {
        if ($accion === 'eliminar') {
            return $this->empleado->eliminar($id)
                ? ['tipo' => 'exito', 'mensaje' => 'Empleado eliminado. Sus asignaciones también fueron retiradas.']
                : ['tipo' => 'error', 'mensaje' => 'No se encontró el empleado.'];
        }

        $nombre = trim($datos['nombre'] ?? '');
        $apellido = trim($datos['apellido'] ?? '');
        $email = strtolower(trim($datos['email'] ?? ''));
        $posicion = trim($datos['posicion'] ?? '');

        if ($nombre === '' || $apellido === '' || $email === '' || $posicion === '') {
            return ['tipo' => 'error', 'mensaje' => 'Complete todos los campos del empleado.'];
        }
        if (mb_strlen($nombre) > 80 || mb_strlen($apellido) > 80 || mb_strlen($email) > 150 || mb_strlen($posicion) > 100) {
            return ['tipo' => 'error', 'mensaje' => 'Revise la longitud de los campos del empleado.'];
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['tipo' => 'error', 'mensaje' => 'Ingrese un correo electrónico válido.'];
        }
        if ($this->empleado->emailExiste($email, $accion === 'editar' ? $id : null)) {
            return ['tipo' => 'error', 'mensaje' => 'Ya existe un empleado con ese correo electrónico.'];
        }

        try {
            if ($accion === 'editar') {
                if (!$this->empleado->actualizar($id, $nombre, $apellido, $email, $posicion)) {
                    return ['tipo' => 'error', 'mensaje' => 'No se encontró el empleado.'];
                }
                return ['tipo' => 'exito', 'mensaje' => 'Empleado actualizado.'];
            }

            $this->empleado->guardar($nombre, $apellido, $email, $posicion);
            return ['tipo' => 'exito', 'mensaje' => 'Empleado registrado.'];
        } catch (mysqli_sql_exception $error) {
            return ['tipo' => 'error', 'mensaje' => $error->getCode() === 1062
                ? 'Ya existe un empleado con ese correo electrónico.'
                : 'No se pudo guardar el empleado. Revise los datos e intente nuevamente.'];
        }
    }
}
