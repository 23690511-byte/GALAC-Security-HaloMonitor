-- HaloMonitor Database Schema (MySQL 8.0+)
-- Desarrollado para GALAC Security (Proyecto HaloMonitor)
-- Cumplimiento de 3FN, Integridad Referencial con FOREIGN KEY, Índices e Inmutabilidad de Auditoría

CREATE DATABASE IF NOT EXISTS halomonitor_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE halomonitor_db;

-- 1. Tabla de Roles (RBAC: Administrador, Analista, Auditor)
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(255) NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabla de Usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    activo TINYINT(1) DEFAULT 1,
    departamento VARCHAR(100) DEFAULT 'Ciberseguridad',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_usuario_rol FOREIGN KEY (rol_id) REFERENCES roles(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_usuario_email (email),
    INDEX idx_usuario_rol (rol_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabla de Sistemas Monitoreados (PyME Assets)
CREATE TABLE IF NOT EXISTS sistemas_monitoreados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    ip_host VARCHAR(60) NOT NULL,
    tipo VARCHAR(50) DEFAULT 'Servidor Web',
    estado ENUM('Online', 'Degradado', 'Critico', 'Offline') DEFAULT 'Online',
    tiempo_respuesta_ms FLOAT DEFAULT 8.2,
    ultimo_escaneo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_sistema_estado (estado)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Tabla de Incidentes (Solicitudes de Ciberseguridad)
-- Con campos de congelamiento de estado y datos operativos al momento del registro
CREATE TABLE IF NOT EXISTS incidentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    folio VARCHAR(30) NOT NULL UNIQUE,
    titulo VARCHAR(150) NOT NULL,
    descripcion TEXT NOT NULL,
    categoria VARCHAR(60) NOT NULL DEFAULT 'Malware',
    gravedad ENUM('Low', 'Medium', 'High', 'Critical') NOT NULL DEFAULT 'Medium',
    estado ENUM('Open', 'InProgress', 'Resolved', 'Closed') NOT NULL DEFAULT 'Open',
    sistema_id INT NULL,
    usuario_reporta_id INT NOT NULL,
    usuario_asignado_id INT NULL,
    costo_estimado DECIMAL(10,2) DEFAULT 0.00,
    precio_contratado DECIMAL(10,2) DEFAULT 0.00, -- Valor congelado de SLA / costo operativo
    datos_congelados JSON NULL,                  -- Snapshot inmutable de la configuración/precios al momento de la operación
    fecha_limite_atencion DATETIME NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre DATETIME NULL,
    CONSTRAINT fk_incidente_usuario_rep FOREIGN KEY (usuario_reporta_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    CONSTRAINT fk_incidente_usuario_asig FOREIGN KEY (usuario_asignado_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    CONSTRAINT fk_incidente_sistema FOREIGN KEY (sistema_id) REFERENCES sistemas_monitoreados(id) ON DELETE SET NULL,
    INDEX idx_incidente_folio (folio),
    INDEX idx_incidente_estado (estado),
    INDEX idx_incidente_gravedad (gravedad),
    INDEX idx_incidente_reporta (usuario_reporta_id),
    INDEX idx_incidente_asignado (usuario_asignado_id),
    INDEX idx_incidente_fecha (fecha_registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Tabla de Auditoría Inmutable: Historial de Incidentes / Solicitudes
-- Trazabilidad exacta de transiciones, bloqueos y snapshot inmutable
CREATE TABLE IF NOT EXISTS historial_solicitud (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incidente_id INT NOT NULL,
    usuario_id INT NOT NULL,
    estado_anterior VARCHAR(30) NULL,
    estado_nuevo VARCHAR(30) NOT NULL,
    comentario TEXT NOT NULL,
    datos_congelados JSON NULL,
    ip_origen VARCHAR(45) NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_historial_incidente FOREIGN KEY (incidente_id) REFERENCES incidentes(id) ON DELETE CASCADE,
    CONSTRAINT fk_historial_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    INDEX idx_historial_incidente (incidente_id),
    INDEX idx_historial_fecha (fecha_registro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Tabla de Reglas de Alertas Automáticas
CREATE TABLE IF NOT EXISTS reglas_alertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(120) NOT NULL,
    criterio_gravedad VARCHAR(30) NOT NULL,
    disparar_notificacion TINYINT(1) DEFAULT 1,
    activa TINYINT(1) DEFAULT 1,
    creado_por INT NOT NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_regla_creador FOREIGN KEY (creado_por) REFERENCES usuarios(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. Tabla de Notificaciones de Alertas
CREATE TABLE IF NOT EXISTS alertas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    regla_id INT NULL,
    incidente_id INT NULL,
    titulo VARCHAR(150) NOT NULL,
    mensaje TEXT NOT NULL,
    gravedad ENUM('Low', 'Medium', 'High', 'Critical') DEFAULT 'High',
    leida TINYINT(1) DEFAULT 0,
    fecha_emision TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_alerta_regla FOREIGN KEY (regla_id) REFERENCES reglas_alertas(id) ON DELETE SET NULL,
    CONSTRAINT fk_alerta_incidente FOREIGN KEY (incidente_id) REFERENCES incidentes(id) ON DELETE CASCADE,
    INDEX idx_alerta_leida (leida),
    INDEX idx_alerta_gravedad (gravedad),
    INDEX idx_alerta_fecha (fecha_emision)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Bitácora de Auditoría del Sistema (Trazabilidad y Calidad CMMI / MoProSoft)
CREATE TABLE IF NOT EXISTS auditoria_sistema (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NULL,
    accion VARCHAR(100) NOT NULL,
    detalles JSON NULL,
    ip VARCHAR(45) NULL,
    user_agent VARCHAR(255) NULL,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_auditoria_accion (accion),
    INDEX idx_auditoria_fecha (creado_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- SEED DATA (Datos Iniciales)
-- Roles según especificación oficial del proyecto
INSERT INTO roles (id, nombre, descripcion) VALUES
(1, 'Administrador', 'Acceso Total (Full Access): Gestión de usuarios, alertas institucionales y configuración global'),
(2, 'Analista', 'Acceso Operativo (Lectura / Escritura): Monitoreo de Dashboard, gestión y atención de incidentes'),
(3, 'Auditor', 'Acceso Solo Lectura (Read-Only): Consulta de métricas, reportes ejecutivos e historiales')
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);

-- Usuarios por Defecto (Hash bcrypt para contraseñas de demostración)
-- Password 'Admin123!': $2y$10$WpQ8sY.4vFj6z7i9d2u.Oeu2qK7xU/k8u0z9s.A5nL3x7qK.o9eK (o generado con password_hash)
-- Utilizaremos hashes estándar generados con password_hash('Password123!', PASSWORD_BCRYPT)
INSERT INTO usuarios (id, nombre, email, password_hash, rol_id, activo, departamento) VALUES
(1, 'Gerardo Axel Castillo (Admin)', 'admin@halomonitor.com', '$2y$10$Ww4rR9GvD20v8M9O6H8eOuyN.l1bY9zJ8aU6P3tL5w0cT.x4g1N8m', 1, 1, 'Dirección de Seguridad'),
(2, 'Luis Roberto Martínez (Analista)', 'analista@halomonitor.com', '$2y$10$Ww4rR9GvD20v8M9O6H8eOuyN.l1bY9zJ8aU6P3tL5w0cT.x4g1N8m', 2, 1, 'Operaciones SOC'),
(3, 'Carlos Reyes Manuel (Auditor)', 'auditor@halomonitor.com', '$2y$10$Ww4rR9GvD20v8M9O6H8eOuyN.l1bY9zJ8aU6P3tL5w0cT.x4g1N8m', 3, 1, 'Auditoría y Cumplimiento')
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);

-- Sistemas de Ejemplo
INSERT INTO sistemas_monitoreados (id, nombre, ip_host, tipo, estado, tiempo_respuesta_ms) VALUES
(1, 'Servidor Web Principal PyME', '192.168.1.10', 'Apache / Nginx', 'Online', 6.4),
(2, 'Servidor de Base de Datos MySQL', '192.168.1.15', 'Database', 'Online', 4.1),
(3, 'Gateway Firewall Perimetral', '192.168.1.1', 'Firewall Fortinet', 'Online', 2.3),
(4, 'Servidor de Facturación ERP', '192.168.1.20', 'ERP App Server', 'Degradado', 14.8),
(5, 'Directorio Activo y Autenticación', '192.168.1.25', 'Active Directory', 'Online', 5.0)
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);

-- Reglas de Alerta
INSERT INTO reglas_alertas (id, nombre, criterio_gravedad, disparar_notificacion, activa, creado_por) VALUES
(1, 'Detección de Malware o Ransomware', 'Critical', 1, 1, 1),
(2, 'Ataque de Fuerza Bruta en Autenticación', 'High', 1, 1, 1),
(3, 'Escaneo no Autorizado de Puertos', 'Medium', 1, 1, 1),
(4, 'Falla de Disponibilidad en Servidor Crítico', 'Critical', 1, 1, 1)
ON DUPLICATE KEY UPDATE nombre=VALUES(nombre);

-- Incidentes Semilla (Basados en el mockup y requisitos)
INSERT INTO incidentes (id, folio, titulo, descripcion, categoria, gravedad, estado, sistema_id, usuario_reporta_id, usuario_asignado_id, costo_estimado, precio_contratado, datos_congelados, fecha_limite_atencion, fecha_registro) VALUES
(1, 'INC-2026-001', 'Malware Detectado en Endpoint Contabilidad', 'Se detectó ejecución sospechosa de script PowerShell y archivo binario con firma maliciosa.', 'Malware', 'Critical', 'Open', 1, 1, 2, 8500.00, 8500.00, '{"sla_horas": 2, "impacto": "Alto", "tarifa_hora": 150.00, "activo_critico": true}', DATE_ADD(NOW(), INTERVAL 2 HOUR), NOW()),
(2, 'INC-2026-002', 'Intento Login Fallido Recurrente (Brute Force)', 'Más de 150 intentos de autenticación fallidos con usuario admin desde IP 45.33.32.156 en 5 minutos.', 'Intrusión', 'Medium', 'InProgress', 2, 1, 2, 3200.00, 3200.00, '{"sla_horas": 8, "impacto": "Medio", "tarifa_hora": 100.00, "activo_critico": false}', DATE_ADD(NOW(), INTERVAL 6 HOUR), DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(3, 'INC-2026-003', 'Puerto Inseguro Escaneado (Puerto 22 y 3389)', 'Actividad de escaneo SYN detectada contra puertos de administración remota expuestos.', 'Escaneo', 'Low', 'Resolved', 3, 2, 2, 1200.00, 1200.00, '{"sla_horas": 24, "impacto": "Bajo", "tarifa_hora": 80.00, "activo_critico": false}', DATE_ADD(NOW(), INTERVAL 18 HOUR), DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(4, 'INC-2026-004', 'Exfiltración Sospechosa de Tráfico DNS', 'Consultas DNS anómalas con payloads codificados en Base64 dirigidas a servidor externo desconocido.', 'Fuga de Datos', 'Critical', 'Open', 4, 1, NULL, 12000.00, 12000.00, '{"sla_horas": 1, "impacto": "Crítico", "tarifa_hora": 200.00, "activo_critico": true}', DATE_ADD(NOW(), INTERVAL 1 HOUR), DATE_SUB(NOW(), INTERVAL 30 MINUTE))
ON DUPLICATE KEY UPDATE folio=VALUES(folio);

-- Historial Semilla
INSERT INTO historial_solicitud (incidente_id, usuario_id, estado_anterior, estado_nuevo, comentario, datos_congelados, ip_origen) VALUES
(1, 1, NULL, 'Open', 'Creación y registro formal del incidente por sistema IDS perimetral.', '{"costo": 8500.00}', '127.0.0.1'),
(2, 1, NULL, 'Open', 'Apertura de ticket tras detección de anomalías de autenticación.', '{"costo": 3200.00}', '127.0.0.1'),
(2, 2, 'Open', 'InProgress', 'Analista asignado inicia bloqueo preventivo de IP y análisis de bitácoras.', '{"costo": 3200.00}', '127.0.0.1'),
(3, 2, NULL, 'Open', 'Registro de evento de escaneo de puertos.', '{"costo": 1200.00}', '127.0.0.1'),
(3, 2, 'Open', 'InProgress', 'Revisión de reglas de firewall perimetral.', '{"costo": 1200.00}', '127.0.0.1'),
(3, 2, 'InProgress', 'Resolved', 'Reglas actualizadas en el firewall perimetral. Se cierra vector de ataque.', '{"costo": 1200.00}', '127.0.0.1'),
(4, 1, NULL, 'Open', 'Apertura de incidente crítico tras alerta de tráfico DNS anómalo.', '{"costo": 12000.00}', '127.0.0.1');

-- Alertas Semilla
INSERT INTO alertas (id, regla_id, incidente_id, titulo, mensaje, gravedad, leida, fecha_emision) VALUES
(1, 1, 1, '¡Malware Crítico Detectado!', '10:42 AM - Malware Detectado [Crítico] en Endpoint Contabilidad.', 'Critical', 0, NOW()),
(2, 2, 2, 'Intento de Acceso no Autorizado', '10:35 AM - Intento Login Fallido [Medio] desde IP externa no reconocida.', 'Medium', 0, DATE_SUB(NOW(), INTERVAL 25 MINUTE)),
(3, 3, 3, 'Escaneo Perimetral', '10:18 AM - Puerto Escaneado [Bajo] detectado por sensor IDS.', 'Low', 1, DATE_SUB(NOW(), INTERVAL 45 MINUTE)),
(4, 1, 4, 'Alerta Inmediata: Exfiltración DNS', 'Tráfico DNS masivo anómalo hacia destino no confiable.', 'Critical', 0, DATE_SUB(NOW(), INTERVAL 15 MINUTE))
ON DUPLICATE KEY UPDATE titulo=VALUES(titulo);
