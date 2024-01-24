-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 09-11-2023 a las 21:35:54
-- Versión del servidor: 10.4.28-MariaDB
-- Versión de PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `prestigetravels`
--

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `CarritoCantidad` (IN `id` INT(11), IN `rut` INT(9), IN `tipo` VARCHAR(1))   IF (tipo = 'p') THEN
SELECT paquetes.id_paquete as id, num_disponibles as disponible, cantidad FROM paquetes LEFT JOIN carrito_paquete ON paquetes.id_paquete = id AND carrito_paquete.rut = rut AND carrito_paquete.id_paquete = paquetes.id_paquete WHERE paquetes.id_paquete = id;
ELSEIF(tipo = 'h')THEN
SELECT hoteles.id_hotel as id, hab_disponibles as disponible, cantidad FROM hoteles LEFT JOIN carrito_hoteles ON hoteles.id_hotel = id AND carrito_hoteles.rut = rut AND carrito_hoteles.id_hotel = hoteles.id_hotel WHERE hoteles.id_hotel = id;
END IF$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DisponibleHotel` (IN `rut` INT)   BEGIN
    SELECT nombre_hotel as nombre, hab_disponibles-cantidad as cantidad FROM carrito_hoteles INNER JOIN hoteles ON carrito_hoteles.rut = rut AND 		carrito_hoteles.id_hotel = hoteles.id_hotel;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `DisponiblePaquete` (IN `rut` INT)   BEGIN
    SELECT nombre as nombre, num_disponibles-cantidad as cantidad FROM carrito_paquete INNER JOIN paquetes ON carrito_paquete.rut = rut AND 		carrito_paquete.id_paquete = paquetes.id_paquete;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `getWishList` (IN `rut` INT)   SELECT wishlist.id_hotel as id, nombre_hotel as nombre, AVG(calificacion_total) as promedio_calificacion, id_wishlist, tipo 
FROM wishlist INNER JOIN hoteles 
ON wishlist.id_hotel = hoteles.id_hotel AND wishlist.id_usuario = rut 
LEFT JOIN hotel_usuario 
ON hotel_usuario.id_hotel = wishlist.id_hotel
GROUP BY id 
UNION ALL 
SELECT wishlist.id_paquete as id, nombre, AVG(calificacion_total) as promedio_calificacion, id_wishlist, tipo 
FROM wishlist INNER JOIN paquetes 
ON wishlist.id_paquete = paquetes.id_paquete AND wishlist.id_usuario = rut 
INNER JOIN paquetes_usuario 
ON paquetes_usuario.id_paquete = wishlist.id_paquete
GROUP BY id$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `mayorReservados` (IN `num` INT)   BEGIN SELECT id_paquete as id, nombre as nombre, precio as precio, num_disponibles as disponible, 'paquete' as tipo FROM paquetes UNION ALL SELECT id_hotel as id, nombre_hotel, precio_por_noche, hab_disponibles, 'hotel' as tipo FROM hoteles ORDER BY disponible DESC LIMIT num; END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `mejorCalificados` (IN `num` INT)   BEGIN 
    SELECT 'hotel' AS tipo, hotel_usuario.id_hotel AS id, hoteles.nombre_hotel AS nombre, hoteles.precio_por_noche AS precio, AVG(hotel_usuario.calificacion_total) AS promedio_calificacion
    FROM hotel_usuario INNER JOIN hoteles
    ON hotel_usuario.id_hotel = hoteles.id_hotel
    GROUP BY hotel_usuario.id_hotel
    UNION ALL SELECT 'paquete' AS tipo, paquetes_usuario.id_paquete AS id, paquetes.nombre AS nombre, paquetes.precio AS precio, AVG(paquetes_usuario.calificacion_total) AS promedio_calificacion
    FROM paquetes_usuario INNER JOIN paquetes
    ON paquetes_usuario.id_paquete = paquetes.id_paquete
    GROUP BY paquetes_usuario.id_paquete
    ORDER BY promedio_calificacion DESC
    LIMIT num;
    END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito_hoteles`
--

CREATE TABLE `carrito_hoteles` (
  `id_hotel` int(11) NOT NULL,
  `rut` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `compra` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Disparadores `carrito_hoteles`
--
DELIMITER $$
CREATE TRIGGER `HotelCascade` BEFORE DELETE ON `carrito_hoteles` FOR EACH ROW IF (OLD.compra = 1) THEN 
UPDATE hoteles SET hoteles.hab_disponibles = hoteles.hab_disponibles - OLD.cantidad WHERE hoteles.id_hotel = OLD.id_hotel;
END IF
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carrito_paquete`
--

CREATE TABLE `carrito_paquete` (
  `id_paquete` int(11) NOT NULL,
  `rut` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `compra` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `carrito_paquete`
--

INSERT INTO `carrito_paquete` (`id_paquete`, `rut`, `cantidad`, `compra`) VALUES
(2, 111111111, 3, 0),
(3, 111111111, 1, 0),
(5, 111111111, 7, 0);

--
-- Disparadores `carrito_paquete`
--
DELIMITER $$
CREATE TRIGGER `paqueteCascade` BEFORE DELETE ON `carrito_paquete` FOR EACH ROW IF(OLD.compra = 1) THEN 
UPDATE paquetes SET paquetes.num_disponibles = paquetes.num_disponibles-OLD.cantidad WHERE paquetes.id_paquete = OLD.id_paquete;
END IF
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hoteles`
--

CREATE TABLE `hoteles` (
  `id_hotel` int(11) NOT NULL,
  `nombre_hotel` varchar(255) NOT NULL,
  `num_estrellas` int(11) NOT NULL,
  `precio_por_noche` int(11) NOT NULL,
  `ciudad` varchar(255) NOT NULL,
  `hab_totales` int(11) NOT NULL,
  `hab_disponibles` int(11) NOT NULL,
  `estacionamiento` tinyint(1) NOT NULL,
  `piscina` tinyint(1) NOT NULL,
  `servicio_lavanderia` tinyint(1) NOT NULL,
  `pet_friendly` tinyint(1) NOT NULL,
  `desayuno` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `hoteles`
--

INSERT INTO `hoteles` (`id_hotel`, `nombre_hotel`, `num_estrellas`, `precio_por_noche`, `ciudad`, `hab_totales`, `hab_disponibles`, `estacionamiento`, `piscina`, `servicio_lavanderia`, `pet_friendly`, `desayuno`) VALUES
(1, 'Hotel X', 4, 150, 'Ciudad A', 50, 9, 1, 1, 1, 0, 1),
(2, 'Hotel Y', 3, 100, 'Ciudad B', 30, 1, 0, 1, 1, 1, 0),
(3, 'Hotel Z', 5, 200, 'Ciudad C', 100, 36, 1, 0, 1, 1, 1),
(4, 'Comodin comodon', 4, 10000, 'Santiago', 50, 50, 1, 1, 1, 0, 1),
(5, 'Gran Palace', 5, 15000, 'Madrid', 100, 80, 1, 1, 1, 0, 1),
(6, 'Sunset Paradise', 4, 8000, 'Cancún', 200, 150, 1, 1, 1, 1, 1),
(7, 'Royal Gardens', 5, 20000, 'Londres', 80, 60, 1, 1, 1, 0, 1),
(8, 'Serene Oasis', 3, 5000, 'Bangkok', 150, 100, 1, 1, 0, 1, 1),
(9, 'Bella Vista', 4, 12000, 'Roma', 60, 50, 1, 0, 1, 0, 1),
(10, 'Marina Bay Resort', 5, 25000, 'Singapur', 200, 180, 1, 1, 1, 0, 1),
(11, 'Paradise Beachfront', 4, 9000, 'Bali', 100, 80, 1, 1, 1, 0, 1),
(12, 'Majestic View', 3, 7000, 'Toronto', 120, 100, 1, 0, 1, 1, 1),
(13, 'Golden Sands', 4, 11000, 'Dubái', 80, 70, 1, 1, 1, 0, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `hotel_usuario`
--

CREATE TABLE `hotel_usuario` (
  `rut` int(11) NOT NULL,
  `id_hotel` int(11) NOT NULL,
  `calificacion_limpieza` int(11) DEFAULT NULL,
  `opinion_limpieza` varchar(500) DEFAULT NULL,
  `calificacion_servicio` int(11) DEFAULT NULL,
  `opinion_servicio` varchar(500) DEFAULT NULL,
  `calificacion_decoracion` int(11) DEFAULT NULL,
  `opinion_decoracion` varchar(500) DEFAULT NULL,
  `calificacion_calidad_camas` int(11) DEFAULT NULL,
  `opinion_calidad_camas` varchar(500) DEFAULT NULL,
  `calificacion_total` decimal(10,2) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `hotel_usuario`
--

INSERT INTO `hotel_usuario` (`rut`, `id_hotel`, `calificacion_limpieza`, `opinion_limpieza`, `calificacion_servicio`, `opinion_servicio`, `calificacion_decoracion`, `opinion_decoracion`, `calificacion_calidad_camas`, `opinion_calidad_camas`, `calificacion_total`, `fecha`) VALUES
(111111111, 1, 4, NULL, 1, NULL, 2, NULL, 1, 'malo', 2.00, '2023-06-06 22:16:07'),
(232323232, 3, 1, NULL, 5, NULL, 2, NULL, 3, NULL, 2.75, '2023-06-06 22:26:37'),
(232323232, 1, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1.00, '2023-06-06 22:26:46'),
(111111111, 2, 3, NULL, 1, NULL, 1, NULL, 3, NULL, 2.00, '2023-06-07 01:57:41'),
(264964721, 4, 5, NULL, 5, NULL, 5, NULL, 5, 'Muy comodo', 5.00, '2023-06-08 02:13:52'),
(264964721, 5, 2, 'gran palacio', 3, NULL, 4, NULL, 2, NULL, 2.75, '2023-06-08 02:15:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquetes`
--

CREATE TABLE `paquetes` (
  `id_paquete` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `aerolinea_ida` varchar(255) NOT NULL,
  `aerolinea_vuelta` varchar(255) NOT NULL,
  `fecha_salida` date NOT NULL,
  `fecha_llegada` date NOT NULL,
  `noches_totales` int(11) NOT NULL,
  `precio` int(11) NOT NULL,
  `num_disponibles` int(11) NOT NULL,
  `num_totales` int(11) NOT NULL,
  `persona_por_paquete` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquetes`
--

INSERT INTO `paquetes` (`id_paquete`, `nombre`, `aerolinea_ida`, `aerolinea_vuelta`, `fecha_salida`, `fecha_llegada`, `noches_totales`, `precio`, `num_disponibles`, `num_totales`, `persona_por_paquete`) VALUES
(1, 'Paquete A', 'Aerolínea A', 'Aerolínea B', '2023-06-01', '2023-06-07', 6, 1000, 5, 20, 2),
(2, 'Paquete B', 'Aerolínea C', 'Aerolínea D', '2023-07-15', '2023-07-22', 7, 1200, 0, 15, 1),
(3, 'Paquete C', 'Aerolínea E', 'Aerolínea F', '2023-08-10', '2023-08-17', 7, 1500, 2, 10, 2),
(4, 'Paquete D', 'Aerolínea G', 'Aerolínea H', '2023-09-05', '2023-09-12', 7, 1100, 4, 18, 2),
(5, 'Paquete E', 'Aerolínea I', 'Aerolínea J', '2023-10-20', '2023-10-27', 7, 1300, 6, 10, 1),
(6, 'Paquete F', 'Aerolínea K', 'Aerolínea L', '2023-11-15', '2023-11-21', 6, 900, 15, 25, 2),
(7, 'Paquete G', 'Aerolínea M', 'Aerolínea N', '2023-12-10', '2023-12-17', 7, 1400, 10, 15, 1),
(8, 'Paquete H', 'Aerolínea O', 'Aerolínea P', '2024-01-05', '2024-01-12', 7, 1600, 8, 12, 2),
(9, 'Explora Adventure', 'SkyJourney', 'StarFlyer', '2023-06-15', '2023-06-20', 5, 5000, 19, 30, 1),
(10, 'Serenity Escape', 'AirWings', 'DreamJet', '2023-07-01', '2023-07-06', 5, 8000, 15, 20, 2),
(11, 'Dreamy Getaway', 'AeroLux', 'SkyVoyage', '2023-08-10', '2023-08-15', 5, 7000, 10, 15, 2),
(12, 'Sunshine Bliss', 'StarTravels', 'AirDreams', '2023-09-05', '2023-09-10', 5, 6000, 8, 12, 1),
(13, 'Jungle Expedition', 'NatureWings', 'WildSky', '2023-10-20', '2023-10-25', 5, 9000, 20, 25, 1),
(14, 'City Escape', 'UrbanFly', 'MetroAir', '2023-11-15', '2023-11-20', 5, 7500, 12, 18, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquetes_hotel`
--

CREATE TABLE `paquetes_hotel` (
  `id_paquete` int(11) NOT NULL,
  `id_hotel` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `paquetes_hotel`
--

INSERT INTO `paquetes_hotel` (`id_paquete`, `id_hotel`) VALUES
(1, 1),
(1, 3),
(2, 1),
(2, 2),
(3, 2),
(3, 3),
(4, 6),
(4, 8),
(5, 4),
(5, 7),
(5, 9),
(6, 6),
(7, 5),
(7, 11),
(7, 12),
(8, 9),
(9, 6),
(9, 13),
(10, 8),
(11, 7),
(11, 10),
(12, 6),
(12, 8),
(12, 11),
(13, 5),
(13, 9),
(14, 7),
(15, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `paquetes_usuario`
--

CREATE TABLE `paquetes_usuario` (
  `rut` int(11) NOT NULL,
  `id_paquete` int(11) NOT NULL,
  `calificacion_calidad_hoteles` int(11) DEFAULT NULL,
  `opinion_calidad_hoteles` varchar(500) DEFAULT NULL,
  `calificacion_transporte` int(11) DEFAULT NULL,
  `opinion_transporte` varchar(500) DEFAULT NULL,
  `calificacion_servicio` int(11) DEFAULT NULL,
  `opinion_servicio` varchar(500) DEFAULT NULL,
  `calificacion_relacion_precio_calidad` int(11) DEFAULT NULL,
  `opinion_relacion_precio_calidad` varchar(500) DEFAULT NULL,
  `calificacion_total` decimal(10,2) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Volcado de datos para la tabla `paquetes_usuario`
--

INSERT INTO `paquetes_usuario` (`rut`, `id_paquete`, `calificacion_calidad_hoteles`, `opinion_calidad_hoteles`, `calificacion_transporte`, `opinion_transporte`, `calificacion_servicio`, `opinion_servicio`, `calificacion_relacion_precio_calidad`, `opinion_relacion_precio_calidad`, `calificacion_total`, `fecha`) VALUES
(111111111, 1, 3, 'adas', NULL, NULL, 2, NULL, NULL, NULL, 2.50, '2023-06-06 22:07:02'),
(232323232, 1, 1, 'No fueron de mi gusto', 4, 'Buen transporte la verdad', 1, 'malo malo', 3, NULL, 2.25, '2023-06-06 22:25:26'),
(232323232, 3, 4, 'Buenos hoteles', 1, NULL, 1, NULL, 1, NULL, 1.75, '2023-06-06 22:25:58'),
(232323232, 8, 3, NULL, 2, NULL, 2, NULL, 5, NULL, 3.00, '2023-06-06 22:26:19'),
(111111111, 3, 4, 'adda', 3, NULL, 2, NULL, 3, NULL, 3.00, '2023-06-07 01:57:30'),
(264964721, 10, 1, 'Serenity escape ya que no quieres volver!!!!', 1, 'MALOO!!', 1, NULL, 1, 'MALO  MALO', 1.00, '2023-06-08 02:14:23'),
(264964721, 9, 5, 'Muy buena calidad la verdad', 3, 'mmm variado', 5, 'buen servicio nada que decir', 5, 'muy  bueno para el precio la verdad', 4.50, '2023-06-08 02:15:03'),
(300000004, 3, 4, NULL, 5, NULL, 5, NULL, 3, NULL, 4.25, '2023-11-09 09:08:02'),
(301234564, 3, 3, 'Me parecio un mal servicio', 2, NULL, 2, NULL, 2, 'malo malo', 2.25, '2023-11-09 20:33:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `rut` int(11) NOT NULL,
  `nombre` varchar(25) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `correo` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`rut`, `nombre`, `fecha_nacimiento`, `correo`, `password`) VALUES
(111111111, 'pepe', '2023-06-01', 'hola@mail.com', '$2y$10$6qXOm9JTFwdOHZNBCuJgCejx8pr29ipHUeAZxblEf4.kGakPVAXsy'),
(123456779, 'usuario', '2023-06-01', 'hola@mail1.com', '$2y$10$19W/NLizRayxXH4GGASY5OiC2c7NbBpo1k2dWHYibH16VYx9juOWS'),
(123456789, 'Juan Perez', '2023-06-01', 'hola@mail2.com', '$2y$10$qHVZDVJpVcR0A3POzJUw3ehtyEqx3w1jPCekkI9nP7qAr5w2qO5dO'),
(232323232, 'Martin Carvajal', '1998-09-10', 'martin.carvajal@ejemplo.com', '$2y$10$da9rwpmjZuX0Vn4qf.zDd.V1cMTVgLrt3.uNZ84/toVVjjOM9/sha'),
(235745312, 'martin ramirez fuenzalida', '2011-06-08', 'dsaasda@dasda.com', '$2y$10$trYzsvTk2FmSn7pwjKQ5OO6t1GSrScC9mLKF4Q0RKNfBCiLjwBTOy'),
(264964721, 'Jose', '1999-06-20', 'jdasdjs.jose@jose.com', '$2y$10$ZVwRNnFCWJ4ivzcKVXkx9O4rsobcXgQnU/0h5PdpaxHI94AkIiYeO'),
(300000004, 'lorenzo', '2000-11-22', 'sda@asda.com', '$2y$10$MAtNsxPtW9471pEKE6uuHOGQLQVlMpPzLRV/eDvt8z8RkGlrH84IW'),
(301234564, 'Manuel mont', '1999-01-14', 'test.test.test@gmail.com', '$2y$10$caAIHmYG46Kc/A32.dXFiukpY7sYGkJISDPdFtJg1tBoGmTVOWvCq'),
(555555555, 'Pedro Rodriguez', '2023-06-01', 'hola@mail3.com', '$2y$10$11BlnUpcM12pRJzNEPtik.yiNp0CzXPPENvwua./u60WJziXt6JeC'),
(987654321, 'sebastian', '2023-06-01', 'mail567@example.com', '$2y$10$n4G2MMCl4TdxdWYXGdqf8uoPh.GeasRK7txOJe2Vru6enXGV1NYp.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `wishlist`
--

CREATE TABLE `wishlist` (
  `id_wishList` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_hotel` int(11) DEFAULT NULL,
  `id_paquete` int(11) DEFAULT NULL,
  `tipo` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `wishlist`
--

INSERT INTO `wishlist` (`id_wishList`, `id_usuario`, `id_hotel`, `id_paquete`, `tipo`) VALUES
(20, 987654321, 3, NULL, 'h'),
(21, 300000004, NULL, 3, 'p'),
(22, 301234564, NULL, 3, 'p');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `carrito_hoteles`
--
ALTER TABLE `carrito_hoteles`
  ADD KEY `rut` (`rut`);

--
-- Indices de la tabla `carrito_paquete`
--
ALTER TABLE `carrito_paquete`
  ADD KEY `FK_rut` (`rut`);

--
-- Indices de la tabla `hoteles`
--
ALTER TABLE `hoteles`
  ADD PRIMARY KEY (`id_hotel`);

--
-- Indices de la tabla `paquetes`
--
ALTER TABLE `paquetes`
  ADD PRIMARY KEY (`id_paquete`);

--
-- Indices de la tabla `paquetes_hotel`
--
ALTER TABLE `paquetes_hotel`
  ADD PRIMARY KEY (`id_paquete`,`id_hotel`),
  ADD KEY `paquetes_hotel_ibfk_2` (`id_hotel`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`rut`);

--
-- Indices de la tabla `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id_wishList`),
  ADD KEY `fk_wishlist` (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id_wishList` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `carrito_hoteles`
--
ALTER TABLE `carrito_hoteles`
  ADD CONSTRAINT `FK_rut_h` FOREIGN KEY (`rut`) REFERENCES `usuarios` (`rut`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `carrito_paquete`
--
ALTER TABLE `carrito_paquete`
  ADD CONSTRAINT `FK_rut_p` FOREIGN KEY (`rut`) REFERENCES `usuarios` (`rut`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `fk_wishlist` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`rut`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
