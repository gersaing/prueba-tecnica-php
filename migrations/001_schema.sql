CREATE DATABASE IF NOT EXISTS empleados;
USE empleados;

-- Tabla de áreas
CREATE TABLE areas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL
);

-- Tabla de roles
CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL
);

-- Tabla de empleados
CREATE TABLE empleados (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,             -- Text
  email VARCHAR(255) NOT NULL,              -- Text (tipo email)
  sexo CHAR(1) NOT NULL,                    -- Radio (M/F)
  area_id INT NOT NULL,                     -- Select (FK hacia areas)
  boletin TINYINT(1) NOT NULL DEFAULT 0,    -- Checkbox
  descripcion TEXT NOT NULL,                -- Textarea
  FOREIGN KEY (area_id) REFERENCES areas(id)
);

-- Tabla pivote empleados-roles
CREATE TABLE empleado_rol (
  empleado_id INT NOT NULL,
  rol_id INT NOT NULL,
  PRIMARY KEY (empleado_id, rol_id),
  FOREIGN KEY (empleado_id) REFERENCES empleados(id),
  FOREIGN KEY (rol_id) REFERENCES roles(id)
);
