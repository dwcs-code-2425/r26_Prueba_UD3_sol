/* ============================================================
   CREAR BASE DE DATOS
   ============================================================ */

CREATE DATABASE IF NOT EXISTS minibank
  DEFAULT CHARACTER SET utf8mb4
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE minibank;


/* ============================================================
   TABLAS
   ============================================================ */

-- Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Tabla de cuentas
CREATE TABLE IF NOT EXISTS cuentas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    importe DECIMAL(10,2) NOT NULL DEFAULT 0,
    titular_id INT NOT NULL,
    FOREIGN KEY (titular_id) REFERENCES usuarios(id)
);


/* ============================================================
   INSERTAR DATOS DE PRUEBA
   ============================================================ */

-- Hashes generados con password_hash(PASSWORD_BCRYPT)
INSERT INTO usuarios (email, password) VALUES
('user1@example.com', '$2y$10$9N/4zLa4uX5BEWKHQtkcbuatBpvsdJ6g6FYLiCQQAZXvJdvjWL1GG'), -- abc123.
('user2@example.com', '$2y$10$C09lDBs6wZSVNiOzo8ztr.uZjotnVR.VomIag/vIae6IANCF7UJlC'); -- abc123.

INSERT INTO cuentas (importe, titular_id) VALUES
(1500.00, 1),
(320.50, 1),
(2000.00, 2),
(50.00, 2);


/* ============================================================
   CREAR USUARIO MYSQL CON PERMISOS SOBRE minibank
   ============================================================ */

-- Cambia la contraseña si quieres
DROP USER IF EXISTS 'minibank_user'@'localhost';

CREATE USER 'minibank_user'@'localhost' IDENTIFIED BY 'minibank_pass';

GRANT ALL PRIVILEGES ON minibank.* TO 'minibank_user'@'localhost';

FLUSH PRIVILEGES;
