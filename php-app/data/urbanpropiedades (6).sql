-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 05-12-2025 a las 04:01:36
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `urbanpropiedades`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carousel_images`
--

CREATE TABLE `carousel_images` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) NOT NULL,
  `link_url` varchar(500) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `file_path` varchar(255) DEFAULT NULL,
  `alt_text` varchar(255) DEFAULT '',
  `display_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carousel_images`
--

INSERT INTO `carousel_images` (`id`, `title`, `description`, `image_url`, `link_url`, `sort_order`, `active`, `created_at`, `updated_at`, `file_path`, `alt_text`, `display_order`, `is_active`) VALUES
(1, '1', NULL, '', NULL, 0, 1, '2025-12-05 02:47:04', '2025-12-05 02:47:04', 'uploads/carousel/1764902824_693247a8a5263.jpg', '1', 1, 1),
(2, '2', NULL, '', NULL, 0, 1, '2025-12-05 02:47:14', '2025-12-05 02:47:14', 'uploads/carousel/1764902834_693247b260f6a.jpg', '2', 2, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `client_favorites`
--

CREATE TABLE `client_favorites` (
  `id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comunas`
--

CREATE TABLE `comunas` (
  `id` int(11) NOT NULL,
  `region_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comunas`
--

INSERT INTO `comunas` (`id`, `region_id`, `name`, `created_at`) VALUES
(1, 7, 'Santiago', '2025-12-04 15:11:58'),
(2, 7, 'Providencia', '2025-12-04 15:11:58'),
(3, 7, 'Las Condes', '2025-12-04 15:11:58'),
(4, 7, 'Vitacura', '2025-12-04 15:11:58'),
(5, 7, 'Lo Barnechea', '2025-12-04 15:11:58'),
(6, 7, 'Ñuñoa', '2025-12-04 15:11:58'),
(7, 7, 'La Reina', '2025-12-04 15:11:58'),
(8, 7, 'Peñalolén', '2025-12-04 15:11:58'),
(9, 7, 'Macul', '2025-12-04 15:11:58'),
(10, 7, 'San Miguel', '2025-12-04 15:11:58'),
(11, 7, 'La Florida', '2025-12-04 15:11:58'),
(12, 7, 'Puente Alto', '2025-12-04 15:11:58'),
(13, 7, 'Maipú', '2025-12-04 15:11:58'),
(14, 7, 'Pudahuel', '2025-12-04 15:11:58'),
(15, 7, 'Cerrillos', '2025-12-04 15:11:58'),
(16, 7, 'Estación Central', '2025-12-04 15:11:58'),
(17, 7, 'Quilicura', '2025-12-04 15:11:58'),
(18, 7, 'Huechuraba', '2025-12-04 15:11:58'),
(19, 7, 'Recoleta', '2025-12-04 15:11:58'),
(20, 7, 'Independencia', '2025-12-04 15:11:58'),
(21, 6, 'Valparaíso', '2025-12-04 15:11:58'),
(22, 6, 'Viña del Mar', '2025-12-04 15:11:58'),
(23, 6, 'Concón', '2025-12-04 15:11:58'),
(24, 6, 'Quilpué', '2025-12-04 15:11:58'),
(25, 6, 'Villa Alemana', '2025-12-04 15:11:58'),
(26, 6, 'Quillota', '2025-12-04 15:11:58'),
(27, 6, 'San Antonio', '2025-12-04 15:11:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `portal_clients`
--

CREATE TABLE `portal_clients` (
  `id` int(11) NOT NULL,
  `razon_social` varchar(255) NOT NULL,
  `rut` varchar(20) NOT NULL,
  `representante_legal` varchar(255) NOT NULL,
  `nombre_completo` varchar(255) NOT NULL,
  `cedula_identidad` varchar(20) NOT NULL,
  `celular` varchar(20) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `alias` varchar(100) NOT NULL,
  `consent_accepted` tinyint(1) DEFAULT 0,
  `consent_date` timestamp NULL DEFAULT NULL,
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(20) DEFAULT 'active',
  `registered_sections` varchar(255) DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `portal_clients`
--

INSERT INTO `portal_clients` (`id`, `razon_social`, `rut`, `representante_legal`, `nombre_completo`, `cedula_identidad`, `celular`, `email`, `password`, `alias`, `consent_accepted`, `consent_date`, `active`, `created_at`, `updated_at`, `status`, `registered_sections`, `last_login_at`) VALUES
(1, 'UrbanGroup', '12.345.678-9', 'Patricio Videla', 'Patricio Videla', '12.345.678-9', '2914125043', 'oligiatielizondo@gmail.com', '$2y$10$lyskFXYWd1zKsDJf4XSwJuy60X/dypeuFGwwHTvFHo/ZtthTnnPlm', 'Patricio', 0, NULL, 1, '2025-12-05 00:24:20', '2025-12-05 02:57:27', 'active', NULL, '2025-12-05 02:57:27'),
(2, '', '23.537.419-6', '', 'Lautaro Olgiati', '', '2914125043', 'shopii.versee@gmail.com', '$2y$10$sVbHOcaDcvnVy.zM23TMvOTBmJbkTkfgrPHH/TDLo5NhFZWuIT9Su', '', 1, '2025-12-05 06:07:46', 1, '2025-12-05 02:07:46', '2025-12-05 02:35:00', 'active', '', '2025-12-05 02:35:00'),
(3, '', '23.847.419-6', '', 'Lautaro Olgiati', '', '2914125043', 'olgiatielizondo@gmail.com', '$2y$10$iChq6rRjJGRreFvGcflX8OEt1/.oqlBASx6sBhu5trR/I0iKvS2yK', '', 1, '2025-12-05 06:40:39', 1, '2025-12-05 02:40:39', '2025-12-05 02:40:39', 'active', '', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `properties`
--

CREATE TABLE `properties` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(15,2) NOT NULL,
  `currency` varchar(10) DEFAULT 'UF',
  `operation_type` enum('Venta','Arriendo') DEFAULT 'Venta',
  `property_type_id` int(11) DEFAULT NULL,
  `region_id` int(11) DEFAULT NULL,
  `comuna_id` int(11) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `bedrooms` int(11) DEFAULT 0,
  `bathrooms` int(11) DEFAULT 0,
  `area` decimal(10,2) DEFAULT NULL,
  `built_area` decimal(10,2) DEFAULT NULL,
  `year_built` int(11) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `active` tinyint(1) DEFAULT 1,
  `user_id` int(11) DEFAULT NULL,
  `section_type` enum('general','terrenos','activos','usa') DEFAULT 'general',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `is_featured` tinyint(1) DEFAULT 0,
  `property_category` varchar(100) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `property_photos`
--

CREATE TABLE `property_photos` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `url` varchar(500) NOT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `property_terreno_details`
--

CREATE TABLE `property_terreno_details` (
  `id` int(11) NOT NULL,
  `property_id` int(11) NOT NULL,
  `zoning_type` varchar(100) DEFAULT NULL COMMENT 'Tipo de zonificación',
  `land_use` varchar(100) DEFAULT NULL COMMENT 'Uso de suelo',
  `buildability_coefficient` decimal(5,2) DEFAULT NULL COMMENT 'Coeficiente de constructibilidad',
  `soil_occupation` decimal(5,2) DEFAULT NULL COMMENT 'Ocupación de suelo',
  `max_height` decimal(10,2) DEFAULT NULL COMMENT 'Altura máxima permitida',
  `has_anteproyecto` tinyint(1) DEFAULT 0 COMMENT 'Tiene anteproyecto aprobado',
  `anteproyecto_details` text DEFAULT NULL COMMENT 'Detalles del anteproyecto',
  `approved_departments` int(11) DEFAULT NULL COMMENT 'Departamentos aprobados',
  `approved_floors` int(11) DEFAULT NULL COMMENT 'Pisos aprobados',
  `approved_parking` int(11) DEFAULT NULL COMMENT 'Estacionamientos aprobados',
  `water_connection` tinyint(1) DEFAULT 0,
  `electricity_connection` tinyint(1) DEFAULT 0,
  `gas_connection` tinyint(1) DEFAULT 0,
  `sewage_connection` tinyint(1) DEFAULT 0,
  `topography` varchar(100) DEFAULT NULL COMMENT 'Topografía del terreno',
  `access_type` varchar(100) DEFAULT NULL COMMENT 'Tipo de acceso',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `property_types`
--

CREATE TABLE `property_types` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `property_types`
--

INSERT INTO `property_types` (`id`, `name`, `icon`, `created_at`) VALUES
(1, 'Casa', 'home', '2025-12-04 15:11:58'),
(2, 'Departamento', 'building', '2025-12-04 15:11:58'),
(3, 'Oficina', 'briefcase', '2025-12-04 15:11:58'),
(4, 'Local Comercial', 'store', '2025-12-04 15:11:58'),
(5, 'Bodega', 'warehouse', '2025-12-04 15:11:58'),
(6, 'Terreno', 'map', '2025-12-04 15:11:58'),
(7, 'Galpón', 'warehouse', '2025-12-04 15:11:58'),
(8, 'Estacionamiento', 'car', '2025-12-04 15:11:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `regions`
--

CREATE TABLE `regions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(10) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `regions`
--

INSERT INTO `regions` (`id`, `name`, `code`, `created_at`) VALUES
(1, 'Arica y Parinacota', 'XV', '2025-12-04 15:11:58'),
(2, 'Tarapacá', 'I', '2025-12-04 15:11:58'),
(3, 'Antofagasta', 'II', '2025-12-04 15:11:58'),
(4, 'Atacama', 'III', '2025-12-04 15:11:58'),
(5, 'Coquimbo', 'IV', '2025-12-04 15:11:58'),
(6, 'Valparaíso', 'V', '2025-12-04 15:11:58'),
(7, 'Metropolitana de Santiago', 'RM', '2025-12-04 15:11:58'),
(8, 'O\'Higgins', 'VI', '2025-12-04 15:11:58'),
(9, 'Maule', 'VII', '2025-12-04 15:11:58'),
(10, 'Ñuble', 'XVI', '2025-12-04 15:11:58'),
(11, 'Biobío', 'VIII', '2025-12-04 15:11:58'),
(12, 'La Araucanía', 'IX', '2025-12-04 15:11:58'),
(13, 'Los Ríos', 'XIV', '2025-12-04 15:11:58'),
(14, 'Los Lagos', 'X', '2025-12-04 15:11:58'),
(15, 'Aysén', 'XI', '2025-12-04 15:11:58'),
(16, 'Magallanes', 'XII', '2025-12-04 15:11:58');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `role` enum('admin','partner') DEFAULT 'partner',
  `active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_active` tinyint(1) DEFAULT 1,
  `phone` varchar(50) DEFAULT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `photo_url` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `name`, `role`, `active`, `created_at`, `updated_at`, `is_active`, `phone`, `company_name`, `photo_url`) VALUES
(1, 'admin', '$2y$10$t1CT2uP3jIFFm.V6jv/nBu2kqYwE.8wcT48OT/MGg9eYEh2tQEn2u', 'admin@urbanpropiedades.cl', 'Administrador', 'admin', 1, '2025-12-04 15:11:58', '2025-12-05 02:46:39', 1, NULL, NULL, NULL),
(2, 'socio1', '$2y$10$6S6LjAVccDcoI2LSU632XOdMp0J5qfLVyEpTZYgcegZJJj2OxM9I.', 'socio@urbanpropiedades.cl', 'Socio Demo', 'partner', 1, '2025-12-04 15:11:58', '2025-12-05 02:55:18', 1, '', NULL, '../uploads/partners/1764903309_6932498d68abf.jpg'),
(3, 'lautaro', '$2y$10$8eKd7f0JEV.oPv9TxS30peRSQGtIHXvw25ViQmap5MefZxxWcLJ6q', 'oligiatielizondo@gmail.com', 'Lautaro', 'admin', 1, '2025-12-05 02:24:22', '2025-12-05 03:00:07', 1, NULL, NULL, NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carousel_images`
--
ALTER TABLE `carousel_images`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `client_favorites`
--
ALTER TABLE `client_favorites`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `client_id` (`client_id`,`property_id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indices de la tabla `comunas`
--
ALTER TABLE `comunas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `region_id` (`region_id`);

--
-- Indices de la tabla `portal_clients`
--
ALTER TABLE `portal_clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_portal_clients_email` (`email`),
  ADD KEY `idx_portal_clients_rut` (`rut`);

--
-- Indices de la tabla `properties`
--
ALTER TABLE `properties`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_type_id` (`property_type_id`),
  ADD KEY `region_id` (`region_id`),
  ADD KEY `comuna_id` (`comuna_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_properties_section` (`section_type`),
  ADD KEY `idx_properties_active` (`active`),
  ADD KEY `idx_properties_featured` (`featured`);

--
-- Indices de la tabla `property_photos`
--
ALTER TABLE `property_photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `property_id` (`property_id`);

--
-- Indices de la tabla `property_terreno_details`
--
ALTER TABLE `property_terreno_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `property_id` (`property_id`);

--
-- Indices de la tabla `property_types`
--
ALTER TABLE `property_types`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `carousel_images`
--
ALTER TABLE `carousel_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `client_favorites`
--
ALTER TABLE `client_favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comunas`
--
ALTER TABLE `comunas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de la tabla `portal_clients`
--
ALTER TABLE `portal_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `properties`
--
ALTER TABLE `properties`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `property_photos`
--
ALTER TABLE `property_photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `property_terreno_details`
--
ALTER TABLE `property_terreno_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `property_types`
--
ALTER TABLE `property_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `regions`
--
ALTER TABLE `regions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `client_favorites`
--
ALTER TABLE `client_favorites`
  ADD CONSTRAINT `client_favorites_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `portal_clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `client_favorites_ibfk_2` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `comunas`
--
ALTER TABLE `comunas`
  ADD CONSTRAINT `comunas_ibfk_1` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `properties`
--
ALTER TABLE `properties`
  ADD CONSTRAINT `properties_ibfk_1` FOREIGN KEY (`property_type_id`) REFERENCES `property_types` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `properties_ibfk_2` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `properties_ibfk_3` FOREIGN KEY (`comuna_id`) REFERENCES `comunas` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `properties_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `property_photos`
--
ALTER TABLE `property_photos`
  ADD CONSTRAINT `property_photos_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `property_terreno_details`
--
ALTER TABLE `property_terreno_details`
  ADD CONSTRAINT `property_terreno_details_ibfk_1` FOREIGN KEY (`property_id`) REFERENCES `properties` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
