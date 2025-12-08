-- SQL script to create and populate the property_types table
-- Run this in your phpMyAdmin SQL tab

-- Drop the table if it already exists to avoid conflicts.
DROP TABLE IF EXISTS `property_types`;

-- Create the new table
CREATE TABLE `property_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert the new property types
INSERT INTO `property_types` (`name`) VALUES
('Bodegas con Renta'),
('Bodegas en Arriendo'),
('Casa Comercial en A (Arriendo)'),
('Casa Comercial en V (Venta)'),
('Casa en Arriendo'),
('Casa en Venta'),
('Centro Vacacional'),
('Complejo Turístico'),
('Depto en Arriendo'),
('Depto en Renta'),
('Depto en Venta'),
('Deptos con Renta'),
('Deptos Inversionistas'),
('Deptos Nuevos'),
('Derechos de Llave'),
('Edificio de Deptos'),
('Edificio de Oficinas'),
('Educacional'),
('Estacionamientos'),
('Fundo'),
('Loft'),
('Loteo'),
('Mall'),
('Motel'),
('Oficinas en Arriendo'),
('Oficinas en Venta'),
('Outlet Mall'),
('P Minera (Propiedad Minera)'),
('Packing'),
('Parcela'),
('Parcelas'),
('Parking'),
('Propiedad Educacional'),
('Propiedad Industrial'),
('Restaurant'),
('Sitio'),
('Strip Center'),
('Supermercado'),
('T en Arriendo (Terreno en Arriendo)'),
('T Indus (Terreno Industrial)'),
('T para 1 Casa (Terreno para 1 Casa)'),
('Viña');
