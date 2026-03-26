-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 26-03-2026 a las 03:30:22
-- Versión del servidor: 8.0.30
-- Versión de PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `outage_mapping`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reports`
--

CREATE TABLE `reports` (
  `id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `category` enum('agua','electricidad','vialidad','otros') COLLATE utf8mb4_unicode_ci NOT NULL,
  `lat` decimal(10,8) NOT NULL,
  `lng` decimal(11,8) NOT NULL,
  `image_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_state` enum('Reportado','En Revisión','Resuelto') COLLATE utf8mb4_unicode_ci DEFAULT 'Reportado',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `reports`
--

INSERT INTO `reports` (`id`, `title`, `description`, `category`, `lat`, `lng`, `image_path`, `current_state`, `created_at`, `updated_at`) VALUES
(3, 'se exploto un transformador', 'mi amiguito diego no tiene luz pq el transformador del poste se exploto no lo dejen esperando ayudenlo', 'electricidad', 11.10982200, -63.92129000, 'uploads/report_69c4701719c2e.png', 'En Revisión', '2026-03-25 23:30:31', '2026-03-25 23:54:47'),
(5, 'falla bien grande', 'se cancelo el examen de ingles', 'electricidad', 10.97834300, -63.87999500, 'uploads/report_69c49d079648c.webp', 'Reportado', '2026-03-26 02:42:15', '2026-03-26 02:42:15'),
(6, 'ayuda!!!!!', 'ayuda aqui hay un chamo que juega for honor, albion y cs2, arrestenlo, ademas no juega bien rocket league', 'otros', 11.08195500, -63.86551200, 'uploads/report_69c49eb1c6790.png', 'Resuelto', '2026-03-26 02:49:21', '2026-03-26 03:23:40'),
(7, 'atencion!!!!!!!', 'aqui vive la leyenda del gaming', 'otros', 11.00732600, -63.82498200, 'uploads/report_69c49f8d35e84.png', 'Reportado', '2026-03-26 02:53:01', '2026-03-26 02:53:01'),
(8, 'FATE CUARTO DE FATECRAO', 'AUXILIO HAY UN TREMENDO HUECO EN ESTA CASA ESPECIFICAMENTE EN ESE CUARTO, TIENEN QUE DEMOLER EXACTAMENTE ESE CUARTO PARA PONER ASFALTO Y PODER TAPAR EL HUECO, DIGAN SI A UN MUNICIPIO SIN HUECOS!!!!!!!!!!!!!', 'vialidad', 10.99423800, -64.02070100, 'uploads/report_69c4a24b34670.webp', 'En Revisión', '2026-03-26 03:04:43', '2026-03-26 03:25:31'),
(9, 'QUEMENLO', 'AQUI ESTA LA ULTIMA PARTE DE CHAVEZ, QUEMENLA', 'otros', 10.98187100, -63.81944100, 'uploads/report_69c4a4319fc34.jpeg', 'Reportado', '2026-03-26 03:12:49', '2026-03-26 03:12:49'),
(10, 'AQUI VENDEN LOS MEJORES PERROS CALIENTES', 'CON EL CODIGO : SOY AMIGO DE RODRIGO BOCHINCHERO. TE LLEVAS UN 80% DE DESCUENTO EN TODO LO QUE QUIERAS. YUPI', 'otros', 10.97505600, -63.82125100, 'uploads/report_69c4a6169259b.gif', 'Reportado', '2026-03-26 03:20:54', '2026-03-26 03:20:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `report_status_history`
--

CREATE TABLE `report_status_history` (
  `id` int NOT NULL,
  `report_id` int NOT NULL,
  `previous_state` enum('Reportado','En Revisión','Resuelto') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `new_state` enum('Reportado','En Revisión','Resuelto') COLLATE utf8mb4_unicode_ci NOT NULL,
  `comments` text COLLATE utf8mb4_unicode_ci,
  `changed_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `report_status_history`
--

INSERT INTO `report_status_history` (`id`, `report_id`, `previous_state`, `new_state`, `comments`, `changed_at`) VALUES
(3, 3, NULL, 'Reportado', 'Reporte creado inicialmente por el usuario.', '2026-03-25 23:30:31'),
(5, 5, NULL, 'Reportado', 'Reporte creado inicialmente por el usuario.', '2026-03-26 02:42:15'),
(6, 6, NULL, 'Reportado', 'Reporte creado inicialmente por el usuario.', '2026-03-26 02:49:21'),
(7, 7, NULL, 'Reportado', 'Reporte creado inicialmente por el usuario.', '2026-03-26 02:53:01'),
(8, 8, NULL, 'Reportado', 'Reporte creado inicialmente por el usuario.', '2026-03-26 03:04:43'),
(9, 9, NULL, 'Reportado', 'Reporte creado inicialmente por el usuario.', '2026-03-26 03:12:49'),
(10, 10, NULL, 'Reportado', 'Reporte creado inicialmente por el usuario.', '2026-03-26 03:20:54');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `reports`
--
ALTER TABLE `reports`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category` (`category`),
  ADD KEY `current_state` (`current_state`);

--
-- Indices de la tabla `report_status_history`
--
ALTER TABLE `report_status_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `report_id` (`report_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `reports`
--
ALTER TABLE `reports`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `report_status_history`
--
ALTER TABLE `report_status_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `report_status_history`
--
ALTER TABLE `report_status_history`
  ADD CONSTRAINT `report_status_history_ibfk_1` FOREIGN KEY (`report_id`) REFERENCES `reports` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
