-- =============================================
-- SISTEMA DE AUTENTICACIÓN PHP - BASE DE DATOS
-- =============================================

-- Crear la base de datos si no existe
CREATE DATABASE IF NOT EXISTS sistema_auth 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_unicode_ci;

-- Seleccionar la base de datos
USE sistema_auth;

-- Crear la tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,  -- Identificador único
    nombre VARCHAR(100) NOT NULL,       -- Nombre completo del usuario
    correo VARCHAR(150) NOT NULL UNIQUE,-- Correo único
    contraseña VARCHAR(255) NOT NULL,   -- Contraseña hasheada (bcrypt)
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
    fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
        ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
