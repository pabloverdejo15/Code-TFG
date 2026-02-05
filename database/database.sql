-- Base de datos: `hello_neighbor`
CREATE DATABASE IF NOT EXISTS `hello_neighbor` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hello_neighbor`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `fecha_registro` datetime DEFAULT current_timestamp(),
  `avatar` varchar(255) DEFAULT NULL,
  `estado` enum('activo','bloqueado') DEFAULT 'activo',
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunidades`
--

CREATE TABLE `comunidades` (  
  `id_comunidad` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `direccion` varchar(255) DEFAULT NULL,
  `codigo_acceso` varchar(20) NOT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `estado` enum('activa','cerrada') DEFAULT 'activa',
  PRIMARY KEY (`id_comunidad`),
  UNIQUE KEY `codigo_acceso` (`codigo_acceso`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_comunidad`
--

CREATE TABLE `usuario_comunidad` (
  `id_usuario` int(11) NOT NULL,
  `id_comunidad` int(11) NOT NULL,
  `rol` enum('admin','vecino') DEFAULT 'vecino',
  `fecha_union` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_usuario`,`id_comunidad`),
  KEY `id_comunidad` (`id_comunidad`),
  CONSTRAINT `fk_uc_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `fk_uc_comunidad` FOREIGN KEY (`id_comunidad`) REFERENCES `comunidades` (`id_comunidad`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `canales`
--

CREATE TABLE `canales` (
  `id_canal` int(11) NOT NULL AUTO_INCREMENT,
  `id_comunidad` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tipo` enum('general','averias','anuncios','social','otros') DEFAULT 'general',
  `descripcion` varchar(255) DEFAULT NULL,
  `fecha_creacion` datetime DEFAULT current_timestamp(),
  `estado` enum('activo','archivado') DEFAULT 'activo',
  PRIMARY KEY (`id_canal`),
  KEY `id_comunidad` (`id_comunidad`),
  CONSTRAINT `fk_canal_comunidad` FOREIGN KEY (`id_comunidad`) REFERENCES `comunidades` (`id_comunidad`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `mensajes`
--

CREATE TABLE `mensajes` (
  `id_mensaje` int(11) NOT NULL AUTO_INCREMENT,
  `id_canal` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `contenido` text NOT NULL,
  `fecha_publicacion` datetime DEFAULT current_timestamp(),
  `editado` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id_mensaje`),
  KEY `id_canal` (`id_canal`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `fk_mensaje_canal` FOREIGN KEY (`id_canal`) REFERENCES `canales` (`id_canal`) ON DELETE CASCADE,
  CONSTRAINT `fk_mensaje_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `avisos`
--

CREATE TABLE `avisos` (
  `id_aviso` int(11) NOT NULL AUTO_INCREMENT,
  `id_comunidad` int(11) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `descripcion` text NOT NULL,
  `tipo` enum('averia','anuncio','reunion') DEFAULT 'anuncio',
  `fecha_publicacion` datetime DEFAULT current_timestamp(),
  `estado` enum('abierto','resuelto') DEFAULT 'abierto',
  PRIMARY KEY (`id_aviso`),
  KEY `id_comunidad` (`id_comunidad`),
  CONSTRAINT `fk_aviso_comunidad` FOREIGN KEY (`id_comunidad`) REFERENCES `comunidades` (`id_comunidad`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_roles`
--

CREATE TABLE `historial_roles` (
  `id_historial` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_comunidad` int(11) NOT NULL,
  `rol_anterior` enum('admin','vecino') NOT NULL,
  `rol_nuevo` enum('admin','vecino') NOT NULL,
  `fecha_cambio` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_historial`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_comunidad` (`id_comunidad`),
  CONSTRAINT `fk_hist_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `fk_hist_comunidad` FOREIGN KEY (`id_comunidad`) REFERENCES `comunidades` (`id_comunidad`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de lecturas de canales
CREATE TABLE usuarios_canales_lectura (
    id_usuario INT NOT NULL,
    id_canal INT NOT NULL,
    fecha_ultimo_acceso DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario, id_canal),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_canal) REFERENCES canales(id_canal) ON DELETE CASCADE
);

-- Tabla de lecturas de mensajes
CREATE TABLE  mensaje_lecturas (
  `id_mensaje` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `fecha_lectura` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id_mensaje`, `id_usuario`),
  CONSTRAINT `fk_lectura_mensaje` FOREIGN KEY (`id_mensaje`) REFERENCES `mensajes` (`id_mensaje`) ON DELETE CASCADE,
  CONSTRAINT `fk_lectura_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
