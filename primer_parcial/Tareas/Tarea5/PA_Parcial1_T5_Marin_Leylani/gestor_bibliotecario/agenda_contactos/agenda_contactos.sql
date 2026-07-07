-- ===================================================
-- BASE DE DATOS: agenda_contactos
-- TAREA 4 - CRUD Agenda de Contactos
-- Programación Avanzada
-- ===================================================

CREATE DATABASE IF NOT EXISTS agenda_contactos;
USE agenda_contactos;

-- =====================================
-- TABLA: contactos
-- =====================================

CREATE TABLE contactos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(150),
    direccion TEXT,
    categoria VARCHAR(50) DEFAULT 'General',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =====================================
-- DATOS DE PRUEBA
-- =====================================

INSERT INTO contactos (nombre, apellido, telefono, correo, direccion, categoria)
VALUES
('María', 'González', '555-0101', 'maria.gonzalez@email.com', 'Av. Principal 123, Col. Centro', 'Familiar'),
('Carlos', 'López', '555-0102', 'carlos.lopez@email.com', 'Calle Roble 45, Col. Reforma', 'Trabajo'),
('Ana', 'Martínez', '555-0103', 'ana.martinez@email.com', 'Blvd. Independencia 789, Col. Norte', 'Amigos'),
('Roberto', 'Hernández', '555-0104', 'roberto.hernandez@email.com', 'Callejón del Sol 12, Col. Sur', 'Trabajo'),
('Laura', 'Ramírez', '555-0105', 'laura.ramirez@email.com', 'Av. Universidad 500, Col. Universitaria', 'General'),
('Pedro', 'Sánchez', '555-0106', 'pedro.sanchez@email.com', 'Calle Luna 67, Col. Jardines', 'Familiar'),
('Sofía', 'Torres', '555-0107', 'sofia.torres@email.com', 'Paseo del Ángel 234, Col. Del Valle', 'Amigos'),
('Miguel', 'Flores', '555-0108', 'miguel.flores@email.com', 'Av. Tecnológico 1500, Col. Industrial', 'Trabajo'),
('Gabriela', 'Díaz', '555-0109', 'gabriela.diaz@email.com', 'Calle Primavera 88, Col. Flores', 'General'),
('Jorge', 'Cruz', '555-0110', 'jorge.cruz@email.com', 'Blvd. del Bosque 321, Col. Forestal', 'Familiar');
