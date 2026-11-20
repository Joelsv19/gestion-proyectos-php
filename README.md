# Sistema de Gestión de Proyectos

Aplicación académica en PHP 8 con MySQLi para registrar proyectos, empleados y las asignaciones entre ambos.

## Ejecución en XAMPP

1. Copie esta carpeta en `C:\xampp2\htdocs\gestion-proyectos`.
2. Inicie Apache y MySQL desde el panel de XAMPP.
3. Importe `base_de_datos.sql` con phpMyAdmin o MySQL.
4. Copie `config/database.example.php` como `config/database.php` y ajuste usuario, contraseña, host y puerto según su instalación.
5. Abra `http://localhost/gestion-proyectos/`.

Las tablas `Proyectos` y `Empleados` almacenan los registros. `Asignaciones` relaciona a varios empleados con varios proyectos y evita duplicar la misma relación.
