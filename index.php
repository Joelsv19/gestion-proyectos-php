<?php
session_start();
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

require_once __DIR__ . '/models/Proyecto.php';
require_once __DIR__ . '/models/Empleado.php';
require_once __DIR__ . '/models/Asignacion.php';
require_once __DIR__ . '/controllers/ProyectoController.php';
require_once __DIR__ . '/controllers/EmpleadoController.php';
require_once __DIR__ . '/controllers/AsignacionController.php';

function e(string|int|null $valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function redirigir(string $seccion): never
{
    header('Location: index.php?seccion=' . rawurlencode($seccion));
    exit;
}

$seccion = $_GET['seccion'] ?? 'proyectos';
if (!in_array($seccion, ['proyectos', 'empleados', 'asignaciones'], true)) {
    $seccion = 'proyectos';
}

try {
    $configuracion = require __DIR__ . '/config/database.php';
    $conexion = new mysqli(
        $configuracion['host'],
        $configuracion['usuario'],
        $configuracion['contrasena'],
        $configuracion['base_datos'],
        (int)$configuracion['puerto']
    );
    $conexion->set_charset('utf8mb4');

    $proyecto = new Proyecto($conexion);
    $empleado = new Empleado($conexion);
    $asignacion = new Asignacion($conexion);
    $controladorProyecto = new ProyectoController($proyecto);
    $controladorEmpleado = new EmpleadoController($empleado);
    $controladorAsignacion = new AsignacionController($asignacion, $proyecto, $empleado);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $accion = $_POST['accion'] ?? '';
        if ($seccion === 'proyectos') {
            $id = filter_var($_POST['proyecto_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $_SESSION['mensaje'] = $controladorProyecto->procesar($accion, (int)($id ?: 0), $_POST);
        } elseif ($seccion === 'empleados') {
            $id = filter_var($_POST['empleado_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
            $_SESSION['mensaje'] = $controladorEmpleado->procesar($accion, (int)($id ?: 0), $_POST);
        } else {
            $_SESSION['mensaje'] = $controladorAsignacion->procesar($accion, $_POST);
        }
        redirigir($seccion);
    }

    $mensaje = $_SESSION['mensaje'] ?? null;
    unset($_SESSION['mensaje']);
    $listaProyectos = $proyecto->listar();
    $listaEmpleados = $empleado->listar();
    $listaAsignaciones = $asignacion->listar();

    require __DIR__ . '/views/encabezado.php';
    if ($seccion === 'proyectos') {
        require __DIR__ . '/views/proyectos.php';
    } elseif ($seccion === 'empleados') {
        require __DIR__ . '/views/empleados.php';
    } else {
        require __DIR__ . '/views/asignaciones.php';
    }
    require __DIR__ . '/views/pie.php';
} catch (Throwable $error) {
    error_log($error->getMessage());
    http_response_code(500);
    require __DIR__ . '/views/error.php';
}
