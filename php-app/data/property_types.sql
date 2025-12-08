-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 01-12-2025 a las 15:40:01
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
-- Estructura de tabla para la tabla `property_types`
--

CREATE TABLE `property_types` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `property_types`
--

INSERT INTO `property_types` (`id`, `name`) VALUES
(1, 'Bodegas con Renta'),
(2, 'Bodegas en Arriendo'),
(3, 'Casa Comercial en A (Arriendo)'),
(4, 'Casa Comercial en V (Venta)'),
(5, 'Casa en Arriendo'),
(6, 'Casa en Venta'),
(7, 'Centro Vacacional'),
(8, 'Complejo Turístico'),
(9, 'Depto en Arriendo'),
(10, 'Depto en Renta'),
(11, 'Depto en Venta'),
(12, 'Deptos con Renta'),
(13, 'Deptos Inversionistas'),
(14, 'Deptos Nuevos'),
(15, 'Derechos de Llave'),
(16, 'Edificio de Deptos'),
(17, 'Edificio de Oficinas'),
(18, 'Educacional'),
(19, 'Estacionamientos'),
(20, 'Fundo'),
(21, 'Loft'),
(22, 'Loteo'),
(23, 'Mall'),
(24, 'Motel'),
(25, 'Oficinas en Arriendo'),
(26, 'Oficinas en Venta'),
(27, 'Outlet Mall'),
(28, 'P Minera (Propiedad Minera)'),
(29, 'Packing'),
(30, 'Parcela'),
(31, 'Parcelas'),
(32, 'Parking'),
(33, 'Propiedad Educacional'),
(34, 'Propiedad Industrial'),
(35, 'Restaurant'),
(36, 'Sitio'),
(37, 'Strip Center'),
(38, 'Supermercado'),
(39, 'T en Arriendo (Terreno en Arriendo)'),
(40, 'T Indus (Terreno Industrial)'),
(41, 'T para 1 Casa (Terreno para 1 Casa)'),
(42, 'Viña');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `property_types`
--
ALTER TABLE `property_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `property_types`
--
ALTER TABLE `property_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
