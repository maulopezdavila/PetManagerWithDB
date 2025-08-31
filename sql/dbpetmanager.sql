/*
# Esquema de Base de Datos - Sistema de Gestión de Mascotas

## Tablas incluidas:
1. usuarios - Para autenticación del sistema
2. mascotas - Información de las mascotas
3. duenos - Información de los dueños
4. visitas_medicas - Historial médico de las mascotas
5. mascota_dueno - Relación muchos a muchos entre mascotas y dueños

## Instrucciones de importación:
1. Crear una base de datos llamada 'sistema_mascotas'
2. Importar este archivo en phpMyAdmin
3. Configurar las credenciales en config/conexion_bd.php
*/

-- Crear base de datos
CREATE DATABASE IF NOT EXISTS sistema_mascotas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_mascotas;

-- Tabla de usuarios para autenticación
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_usuario VARCHAR(50) UNIQUE NOT NULL,
    hash_password VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    activo BOOLEAN DEFAULT TRUE,
    INDEX idx_nombre_usuario (nombre_usuario)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de mascotas
CREATE TABLE mascotas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    especie ENUM('Perro', 'Gato', 'Ave', 'Conejo', 'Otro') NOT NULL,
    raza VARCHAR(100) DEFAULT NULL,
    fecha_nacimiento DATE DEFAULT NULL,
    color TEXT DEFAULT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_nombre (nombre),
    INDEX idx_especie (especie),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de dueños
CREATE TABLE duenos (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    email VARCHAR(100) DEFAULT NULL,
    direccion TEXT DEFAULT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT NOT NULL,
    activo BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_nombre (nombre),
    INDEX idx_telefono (telefono),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de visitas médicas
CREATE TABLE visitas_medicas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    mascota_id INT NOT NULL,
    fecha DATE NOT NULL,
    motivo VARCHAR(200) DEFAULT NULL,
    diagnostico TEXT NOT NULL,
    tratamiento TEXT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    usuario_id INT NOT NULL,
    FOREIGN KEY (mascota_id) REFERENCES mascotas(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_mascota (mascota_id),
    INDEX idx_fecha (fecha),
    INDEX idx_usuario (usuario_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de relación muchos a muchos entre mascotas y dueños
CREATE TABLE mascota_dueno (
    id INT PRIMARY KEY AUTO_INCREMENT,
    mascota_id INT NOT NULL,
    dueno_id INT NOT NULL,
    fecha_asociacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (mascota_id) REFERENCES mascotas(id) ON DELETE CASCADE,
    FOREIGN KEY (dueno_id) REFERENCES duenos(id) ON DELETE CASCADE,
    UNIQUE KEY unique_mascota_dueno (mascota_id, dueno_id),
    INDEX idx_mascota (mascota_id),
    INDEX idx_dueno (dueno_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

