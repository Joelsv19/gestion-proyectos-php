CREATE DATABASE IF NOT EXISTS gestion_proyectos
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE gestion_proyectos;

CREATE TABLE IF NOT EXISTS Proyectos (
    proyecto_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    fecha_inicio DATE NOT NULL,
    fecha_fin DATE NOT NULL,
    PRIMARY KEY (proyecto_id),
    UNIQUE KEY uq_proyectos_nombre (nombre),
    CONSTRAINT chk_proyectos_fechas CHECK (fecha_fin >= fecha_inicio)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS Empleados (
    empleado_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nombre VARCHAR(80) NOT NULL,
    apellido VARCHAR(80) NOT NULL,
    email VARCHAR(150) NOT NULL,
    posicion VARCHAR(100) NOT NULL,
    PRIMARY KEY (empleado_id),
    UNIQUE KEY uq_empleados_email (email)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS Asignaciones (
    asignacion_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
    proyecto_id INT UNSIGNED NOT NULL,
    empleado_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (asignacion_id),
    UNIQUE KEY uq_asignaciones_proyecto_empleado (proyecto_id, empleado_id),
    KEY ix_asignaciones_empleado (empleado_id),
    CONSTRAINT fk_asignaciones_proyecto
        FOREIGN KEY (proyecto_id) REFERENCES Proyectos (proyecto_id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_asignaciones_empleado
        FOREIGN KEY (empleado_id) REFERENCES Empleados (empleado_id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;
