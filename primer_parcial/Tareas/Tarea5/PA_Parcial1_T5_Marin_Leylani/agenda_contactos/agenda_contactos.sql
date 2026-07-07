-- Base de datos: agenda_contactos
CREATE DATABASE IF NOT EXISTS agenda_contactos CHARACTER SET utf8 COLLATE utf8_general_ci;
USE agenda_contactos;

CREATE TABLE IF NOT EXISTS contactos (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(100) NOT NULL,
    apellido    VARCHAR(100) NOT NULL,
    telefono    VARCHAR(20),
    email       VARCHAR(150),
    direccion   VARCHAR(255),
    notas       TEXT,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Datos de prueba
INSERT INTO contactos (nombre, apellido, telefono, email, direccion, notas) VALUES
('Ana',     'García',    '9811234567', 'ana.garcia@email.com',    'Calle 5 de Mayo #10, Campeche',  'Amiga de la universidad'),
('Carlos',  'López',     '9819876543', 'carlos.lopez@email.com',  'Av. Central #45, Mérida',        'Compañero de trabajo'),
('María',   'Martínez',  '9815554433', 'maria.m@email.com',       'Calle Reforma #22, Campeche',    'Vecina'),
('Luis',    'Hernández', '9812223344', NULL,                       NULL,                             NULL),
('Sofía',   'Ramírez',   '9817778899', 'sofia.r@gmail.com',       'Periférico Sur #100, Campeche',  'Contacto de emergencia');
