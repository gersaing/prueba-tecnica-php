CREATE DATABASE IF NOT EXISTS empleados;
USE empleados;


CREATE TABLE areas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL
);


CREATE TABLE roles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL
);

CREATE TABLE empleados (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(255) NOT NULL,             
  email VARCHAR(255) NOT NULL,              
  sexo CHAR(1) NOT NULL,                    
  area_id INT NOT NULL,                     
  boletin TINYINT(1) NOT NULL DEFAULT 0,    
  descripcion TEXT NOT NULL,                
  FOREIGN KEY (area_id) REFERENCES areas(id)
);

CREATE TABLE empleado_rol (
  empleado_id INT NOT NULL,
  rol_id INT NOT NULL,
  PRIMARY KEY (empleado_id, rol_id),
  FOREIGN KEY (empleado_id) REFERENCES empleados(id),
  FOREIGN KEY (rol_id) REFERENCES roles(id)
);

--  áreas 
INSERT INTO areas (nombre) VALUES
('Administración'),
('Ventas'),
('Calidad'),
('Producción');

-- roles 
INSERT INTO roles (nombre) VALUES
('Profesional de proyectos - Desarrollador'),
('Gerente estratégico'),
('Auxiliar administrativo');
