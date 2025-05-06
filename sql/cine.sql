-- BASE DE DATOS DEL CINE
DROP DATABASE IF EXISTS cine;
CREATE DATABASE cine;
USE cine;

-- TABLA USUARIOS
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABLA PELICULAS
CREATE TABLE peliculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT,
    duracion INT,
    clasificacion VARCHAR(10),
    imagen VARCHAR(255),
    estado ENUM('activa','inactiva', 'estreno') DEFAULT 'activa'
);

-- TABLA FUNCIONES
CREATE TABLE funciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pelicula_id INT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    precio DECIMAL(8,2) NOT NULL,
    FOREIGN KEY (pelicula_id) REFERENCES peliculas(id) ON DELETE CASCADE
);

-- TABLA SALAS
CREATE TABLE salas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE  
);

-- TABLA SALA_FUNCION 
CREATE TABLE sala_funcion (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sala_id INT NOT NULL,
    funcion_id INT NOT NULL,
    FOREIGN KEY (sala_id) REFERENCES salas(id) ON DELETE CASCADE,
    FOREIGN KEY (funcion_id) REFERENCES funciones(id) ON DELETE CASCADE
);

-- TABLA ASIENTOS 
CREATE TABLE asientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sala_funcion_id INT NOT NULL,
    fila CHAR(1) NOT NULL,
    numero INT NOT NULL,
    estado ENUM('libre','reservado') DEFAULT 'libre',
    UNIQUE(sala_funcion_id, fila, numero),
    FOREIGN KEY (sala_funcion_id) REFERENCES sala_funcion(id) ON DELETE CASCADE
);

-- TABLA RESERVAS
CREATE TABLE reservas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    funcion_id INT NOT NULL,
    fecha_reserva TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    total DECIMAL(8,2) NOT NULL,
    estado ENUM('pendiente','pagado','cancelado') DEFAULT 'pendiente',
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (funcion_id) REFERENCES funciones(id) ON DELETE CASCADE
);

-- TABLA DETALLE DE RESERVA
CREATE TABLE reserva_asientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,
    asiento_id INT NOT NULL,
    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE,
    FOREIGN KEY (asiento_id) REFERENCES asientos(id) ON DELETE CASCADE
);

-- TABLA PAGOS
CREATE TABLE pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reserva_id INT NOT NULL,
    metodo_pago VARCHAR(50) NOT NULL,
    monto DECIMAL(8,2) NOT NULL,
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reserva_id) REFERENCES reservas(id) ON DELETE CASCADE
);

-- PELICULAS EJEMPLO
INSERT INTO peliculas (titulo, descripcion, duracion, clasificacion, imagen, estado) VALUES
('Brokeback Mountain', 'Drama y romance en las montañas', 134, 'B15', 'mountain.jpg', 'activa'),
('Titanic', 'Drama y romance en el océano', 195, 'B', 'titanic.jpg', 'estreno');

-- FUNCIONES EJEMPLO
INSERT INTO funciones (pelicula_id, fecha, hora, precio) VALUES
(1, '2025-05-05', '18:00:00', 60.00),
(1, '2025-05-06', '21:00:00', 65.00),
(2, '2025-05-05', '19:30:00', 55.00);

-- SALAS 
INSERT INTO salas (nombre) VALUES
('Sala 1'), ('Sala 2'), ('Sala 3'), ('Sala 4'), ('Sala 5');

-- UNA RELACION DE LA SALAFUNCION
INSERT INTO sala_funcion (sala_id, funcion_id) VALUES
(1, 1),  -- Sala 1 con Funcion 1
(1, 2),  
(2, 3);  

-- INSERTA ASIENTOS PARA LA FUNCION ESPECIFICA
INSERT INTO asientos (sala_funcion_id, fila, numero)
SELECT sf.id, f.fila, n.numero
FROM sala_funcion AS sf
JOIN salas AS s ON sf.sala_id = s.id
CROSS JOIN (
  SELECT 'A' AS fila UNION ALL SELECT 'B' UNION ALL SELECT 'C' UNION ALL SELECT 'D'
  UNION ALL SELECT 'E' UNION ALL SELECT 'F' UNION ALL SELECT 'G' UNION ALL SELECT 'H'
  UNION ALL SELECT 'I' UNION ALL SELECT 'J'
) AS f
CROSS JOIN (
  SELECT 1 AS numero UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4
  UNION ALL SELECT 5 UNION ALL SELECT 6 UNION ALL SELECT 7 UNION ALL SELECT 8
  UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12
  UNION ALL SELECT 13 UNION ALL SELECT 14 UNION ALL SELECT 15 UNION ALL SELECT 16
  UNION ALL SELECT 17 UNION ALL SELECT 18 UNION ALL SELECT 19 UNION ALL SELECT 20
) AS n;

-- USUARIOSPRUEBA
INSERT INTO usuarios (nombre, email, password) VALUES
('Alfredo Palacios', 'yiyoxd@gmail.com', 'yiyostore'),
('Daniela Aldaco', 'daniald@gmail.com', 'checo123'),
('Sofia Gutierrez', 'sofiagtz@gmail.com', 'purin24');

-- RESERVA Y ASIENTOS RESERVADOS
INSERT INTO reservas (usuario_id, funcion_id, total, estado) VALUES
(1, 1, 120.00, 'pagado');

-- ASIENTOS RESERVADOS
INSERT INTO reserva_asientos (reserva_id, asiento_id) VALUES
(1, (SELECT id FROM asientos WHERE sala_funcion_id = 1 AND fila = 'A' AND numero = 1)),
(1, (SELECT id FROM asientos WHERE sala_funcion_id = 1 AND fila = 'A' AND numero = 2));

-- ACTIALIZACION DEL ESTADO
UPDATE asientos SET estado = 'reservado' WHERE sala_funcion_id = 1 AND fila = 'A' AND numero IN (1, 2);

-- VERIFICA LOS ASIENTOS
SELECT s.nombre AS sala, a.fila, a.numero, a.estado
FROM salas s
JOIN sala_funcion sf ON s.id = sf.sala_id
JOIN asientos a ON sf.id = a.sala_funcion_id
WHERE s.id = 1;