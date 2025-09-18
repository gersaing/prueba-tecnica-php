SET sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION';
SET time_zone = '+00:00';
DROP DATABASE IF EXISTS empleados;
CREATE DATABASE empleados
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE empleados;

-- -------------------------
-- Tablas base
-- -------------------------
CREATE TABLE areas (
  id     INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE roles (
  id     INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE empleados (
  id          INT AUTO_INCREMENT PRIMARY KEY,
  nombre      VARCHAR(255) NOT NULL,
  email       VARCHAR(255) NOT NULL,
  sexo        CHAR(1)      NOT NULL,
  area_id     INT          NOT NULL,
  boletin     TINYINT(1)   NOT NULL DEFAULT 0,
  descripcion TEXT         NOT NULL,
  CONSTRAINT fk_empleado_area
    FOREIGN KEY (area_id) REFERENCES areas(id),
  CONSTRAINT uq_empleados_email
    UNIQUE (email)                 -- ← email único (colación CI = no distingue mayúsculas)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE empleado_rol (
  empleado_id INT NOT NULL,
  rol_id      INT NOT NULL,
  PRIMARY KEY (empleado_id, rol_id),
  CONSTRAINT fk_empleado_rol_emp
    FOREIGN KEY (empleado_id) REFERENCES empleados(id),
  CONSTRAINT fk_empleado_rol_rol
    FOREIGN KEY (rol_id)      REFERENCES roles(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -------------------------
-- Datos iniciales
-- -------------------------
INSERT INTO areas (nombre) VALUES
('Administración'),
('Ventas'),
('Calidad'),
('Producción');

INSERT INTO roles (nombre) VALUES
('Profesional de proyectos - Desarrollador'),
('Gerente estratégico'),
('Auxiliar administrativo');


