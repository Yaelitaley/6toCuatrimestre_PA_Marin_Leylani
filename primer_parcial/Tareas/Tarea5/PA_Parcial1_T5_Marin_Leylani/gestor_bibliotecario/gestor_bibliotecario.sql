CREATE DATABASE gestor_biblioteca;
USE gestor_biblioteca;

-- =====================================
-- TABLA: roles
-- =====================================

CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- =====================================
-- TABLA: usuarios
-- =====================================

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    telefono VARCHAR(20),
    id_rol INT NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_usuario_rol
        FOREIGN KEY (id_rol)
        REFERENCES roles(id)
);

-- =====================================
-- TABLA: categorias
-- =====================================

CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT
);

-- =====================================
-- TABLA: autores
-- =====================================

CREATE TABLE autores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    nacionalidad VARCHAR(100),
    fecha_nacimiento DATE
);

-- =====================================
-- TABLA: libros
-- =====================================

CREATE TABLE libros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    isbn VARCHAR(20) NOT NULL UNIQUE,
    anio_publicacion YEAR,
    editorial VARCHAR(150),
    cantidad INT NOT NULL DEFAULT 0,
    disponibles INT NOT NULL DEFAULT 0,
    id_categoria INT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_libro_categoria
        FOREIGN KEY (id_categoria)
        REFERENCES categorias(id)
);

-- =====================================
-- TABLA: libro_autor
-- RELACIÓN MUCHOS A MUCHOS
-- =====================================

CREATE TABLE libro_autor (
    id_libro INT NOT NULL,
    id_autor INT NOT NULL,

    PRIMARY KEY (id_libro, id_autor),

    CONSTRAINT fk_la_libro
        FOREIGN KEY (id_libro)
        REFERENCES libros(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_la_autor
        FOREIGN KEY (id_autor)
        REFERENCES autores(id)
        ON DELETE CASCADE
);

-- =====================================
-- TABLA: prestamos
-- =====================================

CREATE TABLE prestamos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha_prestamo DATE NOT NULL,
    fecha_devolucion DATE NOT NULL,
    estado ENUM('prestado', 'devuelto', 'retrasado')
        DEFAULT 'prestado',

    CONSTRAINT fk_prestamo_usuario
        FOREIGN KEY (id_usuario)
        REFERENCES usuarios(id)
);

-- =====================================
-- TABLA: detalle_prestamo
-- =====================================

CREATE TABLE detalle_prestamo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_prestamo INT NOT NULL,
    id_libro INT NOT NULL,
    cantidad INT NOT NULL DEFAULT 1,

    CONSTRAINT fk_detalle_prestamo
        FOREIGN KEY (id_prestamo)
        REFERENCES prestamos(id)
        ON DELETE CASCADE,

    CONSTRAINT fk_detalle_libro
        FOREIGN KEY (id_libro)
        REFERENCES libros(id)
);

-- =====================================
-- DATOS INICIALES
-- =====================================

INSERT INTO roles(nombre)
VALUES
('Administrador'),
('Bibliotecario');

INSERT INTO categorias(nombre, descripcion)
VALUES
('Programación', 'Libros relacionados con desarrollo de software'),
('Bases de Datos', 'Libros de SQL y modelado'),
('Redes', 'Libros relacionados con redes y telecomunicaciones');

INSERT INTO autores(nombre, apellido, nacionalidad, fecha_nacimiento)
VALUES
('Robert', 'Martin', 'Estados Unidos', '1952-12-05'),
('Gabriel', 'García Márquez', 'Colombia', '1927-03-06'),
('Bjarne', 'Stroustrup', 'Dinamarca', '1950-12-30');

INSERT INTO libros(
    titulo,
    isbn,
    anio_publicacion,
    editorial,
    cantidad,
    disponibles,
    id_categoria
)
VALUES
(
    'Clean Code',
    '9780132350884',
    2008,
    'Prentice Hall',
    10,
    10,
    1
),
(
    'Cien Años de Soledad',
    '9780307474728',
    1967,
    'Sudamericana',
    5,
    5,
    1
),
(
    'The C++ Programming Language',
    '9780321563842',
    2013,
    'Addison-Wesley',
    7,
    7,
    1
);

INSERT INTO libro_autor(id_libro, id_autor)
VALUES
(1,1),
(2,2),
(3,3);

INSERT INTO usuarios(
    nombre,
    apellido,
    correo,
    password,
    telefono,
    id_rol
)
VALUES
(
    'Mario',
    'Segovia',
    'admin@biblioteca.com',
    '123456',
    '9810000000',
    1
),
(
    'Juan',
    'Pérez',
    'juan@biblioteca.com',
    '123456',
    '9811111111',
    2
);