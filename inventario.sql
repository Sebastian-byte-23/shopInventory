-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-11-2024 a las 18:16:32
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inventario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `categoria_padre_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nombre`, `categoria_padre_id`) VALUES
(30, 'Galletas', NULL),
(31, 'Mckay', 30),
(32, 'Costa', 30),
(33, 'Nestle', 30),
(34, 'Fruna', 30),
(35, 'Alfajores', NULL),
(36, 'Bon o Bon', 35),
(37, 'Leches', NULL),
(38, 'Colun', 37),
(39, 'Toddy', 30),
(40, 'Pastillas', NULL),
(41, 'Ambrosoli', 40),
(43, 'Oreo', 30),
(44, 'Queques', NULL),
(45, 'Nutra Bien', 44),
(46, 'Yogu Yogu', 37),
(47, 'Jugos Tetra pak', NULL),
(48, 'Watts', 47),
(49, 'Arcor', 30),
(50, 'Cereales', NULL),
(51, 'Colacao', 50),
(52, 'Alimentos en Polvos', NULL),
(53, 'Milo', 52),
(54, 'Dos en Uno', 30),
(55, 'Bon o Bon', 30),
(56, 'Gomitas', NULL),
(57, 'Eucaliptus', 56),
(58, 'M&M', 40),
(59, 'Lonco Leche', 37),
(60, 'Barras', NULL),
(61, 'Dos en Uno', 60),
(62, 'Mani', NULL),
(63, 'Arcor', 62),
(64, 'Costa', 62),
(65, 'Oba Oba', 60),
(66, 'mani salado', 62),
(67, 'Bebidas', NULL),
(68, 'Express', 67);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleado`
--

CREATE TABLE `empleado` (
  `id_empleado` int(11) NOT NULL,
  `rut` varchar(12) NOT NULL,
  `tipo_empleado` varchar(45) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `apellido` varchar(200) NOT NULL,
  `direccion_empleado` varchar(200) NOT NULL,
  `telefono` varchar(12) NOT NULL,
  `email` varchar(200) NOT NULL,
  `local_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `empleado`
--

INSERT INTO `empleado` (`id_empleado`, `rut`, `tipo_empleado`, `nombre`, `apellido`, `direccion_empleado`, `telefono`, `email`, `local_id`) VALUES
(1, '20.866.870-6', 'Administrador', 'Sebastian', 'Urizar', 'av. pique carlos 109', '930883662', 'seba@gmail.com', 1),
(2, '12.531.244-6', 'Vendedor', 'Sebastian', 'Suazo', 'av. pique carlos 109', '930883662', 'seba@gmail.com', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `historial_movimientos`
--

CREATE TABLE `historial_movimientos` (
  `id_movimiento` int(11) NOT NULL,
  `id_producto` int(11) DEFAULT NULL,
  `fecha_movimiento` timestamp NOT NULL DEFAULT current_timestamp(),
  `cantidad` int(11) DEFAULT NULL,
  `tipo_movimiento` varchar(50) DEFAULT NULL,
  `usuario` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `historial_movimientos`
--

INSERT INTO `historial_movimientos` (`id_movimiento`, `id_producto`, `fecha_movimiento`, `cantidad`, `tipo_movimiento`, `usuario`) VALUES
(32, 1, '2024-11-10 15:33:54', 10, 'agregación', 'Administrador'),
(33, 2, '2024-11-10 15:36:41', 12, 'agregación', 'Administrador'),
(34, 3, '2024-11-10 15:39:28', 6, 'agregación', 'Administrador'),
(35, 4, '2024-11-10 15:40:40', 5, 'agregación', 'Administrador'),
(36, 5, '2024-11-10 15:41:52', 8, 'agregación', 'Administrador'),
(37, 6, '2024-11-10 15:45:19', 20, 'agregación', 'Administrador'),
(38, 7, '2024-11-10 15:47:27', 8, 'agregación', 'Administrador'),
(39, 8, '2024-11-10 15:48:27', 5, 'agregación', 'Administrador'),
(40, 9, '2024-11-10 15:50:39', 23, 'agregación', 'Administrador'),
(41, 10, '2024-11-10 15:52:00', 23, 'agregación', 'Administrador'),
(42, 11, '2024-11-10 15:54:07', 20, 'agregación', 'Administrador'),
(43, 12, '2024-11-10 15:56:30', 32, 'agregación', 'Administrador'),
(44, 13, '2024-11-10 15:58:01', 10, 'agregación', 'Administrador'),
(45, 14, '2024-11-10 15:59:04', 25, 'agregación', 'Administrador'),
(46, 15, '2024-11-10 16:01:39', 15, 'agregación', 'Administrador'),
(47, 16, '2024-11-10 16:03:59', 29, 'agregación', 'Administrador'),
(48, 17, '2024-11-10 16:05:10', 20, 'agregación', 'Administrador'),
(49, 18, '2024-11-10 16:06:49', 10, 'agregación', 'Administrador'),
(50, 19, '2024-11-10 16:09:23', 10, 'agregación', 'Administrador'),
(51, 20, '2024-11-10 16:10:38', 12, 'agregación', 'Administrador'),
(52, 21, '2024-11-10 16:14:23', 7, 'agregación', 'Administrador'),
(53, 22, '2024-11-10 16:15:45', 9, 'agregación', 'Administrador'),
(54, 23, '2024-11-10 16:16:28', 20, 'agregación', 'Administrador'),
(55, 24, '2024-11-10 16:17:28', 12, 'agregación', 'Administrador'),
(56, 25, '2024-11-10 16:18:24', 14, 'agregación', 'Administrador'),
(57, 26, '2024-11-10 16:19:38', 13, 'agregación', 'Administrador'),
(58, 27, '2024-11-10 16:20:22', 13, 'agregación', 'Administrador'),
(59, 28, '2024-11-10 16:22:06', 20, 'agregación', 'Administrador'),
(60, 29, '2024-11-10 16:23:15', 32, 'agregación', 'Administrador'),
(61, 30, '2024-11-10 16:24:04', 15, 'agregación', 'Administrador'),
(62, 31, '2024-11-10 16:25:25', 10, 'agregación', 'Administrador'),
(63, 32, '2024-11-10 16:26:26', 25, 'agregación', 'Administrador'),
(64, 33, '2024-11-10 16:27:17', 10, 'agregación', 'Administrador'),
(65, 34, '2024-11-10 16:28:47', 16, 'agregación', 'Administrador'),
(66, 34, '2024-11-10 16:29:12', 16, 'actualización', 'Administrador'),
(67, 35, '2024-11-10 16:30:19', 17, 'agregación', 'Administrador'),
(68, 36, '2024-11-10 16:31:18', 14, 'agregación', 'Administrador'),
(69, 37, '2024-11-10 16:32:49', 15, 'agregación', 'Administrador'),
(70, 38, '2024-11-10 16:33:30', 12, 'agregación', 'Administrador'),
(71, 39, '2024-11-10 16:34:53', 9, 'agregación', 'Administrador'),
(72, 40, '2024-11-10 16:36:08', 11, 'agregación', 'Administrador'),
(73, 41, '2024-11-10 16:37:10', 11, 'agregación', 'Administrador'),
(74, 42, '2024-11-10 16:37:52', 16, 'agregación', 'Administrador'),
(75, 43, '2024-11-10 16:38:42', 28, 'agregación', 'Administrador'),
(76, 44, '2024-11-10 16:39:24', 15, 'agregación', 'Administrador'),
(77, 45, '2024-11-10 16:40:26', 20, 'agregación', 'Administrador'),
(78, 46, '2024-11-10 16:41:13', 20, 'agregación', 'Administrador'),
(79, 47, '2024-11-10 16:42:21', 70, 'agregación', 'Administrador'),
(80, 48, '2024-11-10 16:43:22', 26, 'agregación', 'Administrador'),
(81, 49, '2024-11-10 16:44:40', 23, 'agregación', 'Administrador'),
(82, 50, '2024-11-10 16:45:41', 20, 'agregación', 'Administrador'),
(84, 1, '2024-11-12 00:00:27', 10, 'actualización', 'Administrador'),
(85, 1, '2024-11-12 00:05:45', 10, 'actualización', 'Administrador'),
(86, 1, '2024-11-12 00:07:32', 10, 'actualización', 'Administrador'),
(87, 1, '2024-11-12 00:08:09', 10, 'actualización', 'Administrador'),
(88, 1, '2024-11-12 00:08:11', 10, 'actualización', 'Administrador'),
(89, 1, '2024-11-12 00:08:19', 10, 'actualización', 'Administrador'),
(90, 1, '2024-11-12 00:08:24', 10, 'actualización', 'Administrador'),
(91, 1, '2024-11-12 00:08:27', 10, 'actualización', 'Administrador'),
(92, 1, '2024-11-12 00:08:29', 10, 'actualización', 'Administrador'),
(93, 1, '2024-11-12 00:08:30', 10, 'actualización', 'Administrador'),
(94, 1, '2024-11-12 00:08:37', 10, 'actualización', 'Administrador'),
(95, 1, '2024-11-12 00:08:41', 10, 'actualización', 'Administrador'),
(96, 1, '2024-11-12 00:08:44', 10, 'actualización', 'Administrador'),
(97, 1, '2024-11-12 00:08:56', 10, 'actualización', 'Administrador'),
(98, 1, '2024-11-12 00:09:03', 10, 'actualización', 'Administrador'),
(99, 1, '2024-11-12 00:09:09', 10, 'actualización', 'Administrador'),
(100, 1, '2024-11-12 00:19:16', 10, 'actualización', 'Administrador'),
(101, 1, '2024-11-12 04:56:45', 8, 'actualización', 'Administrador'),
(102, 1, '2024-11-12 04:57:35', 8, 'actualización', 'Administrador'),
(103, 1, '2024-11-12 04:57:36', 8, 'actualización', 'Administrador'),
(104, 1, '2024-11-12 04:58:21', 8, 'actualización', 'Administrador'),
(105, 1, '2024-11-12 04:58:23', 8, 'actualización', 'Administrador'),
(106, 1, '2024-11-12 04:58:26', 8, 'actualización', 'Administrador'),
(107, 1, '2024-11-12 04:58:28', 8, 'actualización', 'Administrador'),
(108, 1, '2024-11-12 04:58:42', 8, 'actualización', 'Administrador'),
(109, 1, '2024-11-12 04:58:43', 8, 'actualización', 'Administrador'),
(110, 1, '2024-11-12 04:58:53', 8, 'actualización', 'Administrador'),
(111, 1, '2024-11-12 04:59:12', 8, 'actualización', 'Administrador'),
(112, 1, '2024-11-12 04:59:37', 8, 'actualización', 'Administrador'),
(113, 1, '2024-11-12 04:59:40', 8, 'actualización', 'Administrador'),
(114, 1, '2024-11-12 04:59:41', 8, 'actualización', 'Administrador'),
(115, 1, '2024-11-12 04:59:43', 8, 'actualización', 'Administrador'),
(116, 1, '2024-11-12 04:59:52', 8, 'actualización', 'Administrador'),
(117, 1, '2024-11-12 04:59:53', 8, 'actualización', 'Administrador'),
(118, 1, '2024-11-12 04:59:55', 8, 'actualización', 'Administrador'),
(119, 1, '2024-11-12 05:00:01', 8, 'actualización', 'Administrador'),
(120, 1, '2024-11-12 05:00:06', 8, 'actualización', 'Administrador'),
(121, 52, '2024-11-12 22:49:51', 20, 'agregación', NULL),
(122, 52, '2024-11-12 22:50:58', 20, 'actualización', NULL),
(123, 52, '2024-11-12 22:52:23', 20, 'actualización', 'Administrador'),
(124, 53, '2024-11-13 03:25:32', 24, 'agregación', 'Administrador'),
(125, 53, '2024-11-13 03:26:25', 24, 'actualización', 'Administrador'),
(126, 7, '2024-11-13 04:23:03', 8, 'actualización', 'Administrador'),
(127, 12, '2024-11-13 04:23:33', 32, 'actualización', 'Administrador'),
(128, 12, '2024-11-13 04:27:10', 32, 'actualización', 'Administrador'),
(129, 12, '2024-11-13 04:27:12', 32, 'actualización', 'Administrador'),
(130, 12, '2024-11-13 04:28:25', 32, 'actualización', 'Administrador'),
(131, 12, '2024-11-13 04:28:35', 32, 'actualización', 'Administrador'),
(132, 12, '2024-11-13 04:28:36', 32, 'actualización', 'Administrador'),
(133, 12, '2024-11-13 04:29:52', 32, 'actualización', 'Administrador'),
(134, 12, '2024-11-13 04:29:54', 32, 'actualización', 'Administrador'),
(135, 12, '2024-11-13 04:30:25', 32, 'actualización', 'Administrador'),
(136, 12, '2024-11-13 04:30:34', 32, 'actualización', 'Administrador'),
(137, 12, '2024-11-13 04:30:46', 32, 'actualización', 'Administrador'),
(138, 12, '2024-11-13 04:30:47', 32, 'actualización', 'Administrador'),
(139, 2, '2024-11-13 04:30:56', 8, 'actualización', 'Administrador'),
(140, 2, '2024-11-13 04:31:52', 8, 'actualización', 'Administrador'),
(141, 2, '2024-11-13 04:31:56', 8, 'actualización', 'Administrador'),
(142, 2, '2024-11-13 04:32:14', 8, 'actualización', 'Administrador'),
(143, 2, '2024-11-13 04:32:18', 8, 'actualización', 'Administrador'),
(144, 2, '2024-11-13 04:32:28', 8, 'actualización', 'Administrador'),
(145, 2, '2024-11-13 04:32:30', 8, 'actualización', 'Administrador'),
(146, 2, '2024-11-13 04:32:46', 8, 'actualización', 'Administrador'),
(147, 2, '2024-11-13 04:32:48', 8, 'actualización', 'Administrador'),
(148, 2, '2024-11-13 04:33:06', 8, 'actualización', 'Administrador'),
(149, 2, '2024-11-13 04:33:20', 8, 'actualización', 'Administrador'),
(150, 2, '2024-11-13 04:33:23', 8, 'actualización', 'Administrador'),
(151, 24, '2024-11-13 04:33:50', 12, 'actualización', 'Administrador'),
(152, 24, '2024-11-13 04:34:01', 12, 'actualización', 'Administrador'),
(153, 24, '2024-11-13 04:34:02', 12, 'actualización', 'Administrador'),
(154, 24, '2024-11-13 04:34:05', 12, 'actualización', 'Administrador'),
(155, 24, '2024-11-13 04:34:08', 12, 'actualización', 'Administrador'),
(156, 24, '2024-11-13 04:34:35', 12, 'actualización', 'Administrador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `local`
--

CREATE TABLE `local` (
  `id_local` int(11) NOT NULL,
  `tipo_local` varchar(50) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `direccion` varchar(200) NOT NULL,
  `telefono` varchar(12) NOT NULL,
  `email` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `local`
--

INSERT INTO `local` (`id_local`, `tipo_local`, `nombre`, `direccion`, `telefono`, `email`) VALUES
(1, 'casa matriz', 'Minimarket \"Tio maury\"', 'av. pique carlos 109', '930883662', 'seba@gmail.com'),
(2, 'sucursal', 'Local 2', 'Avenida 10983', '847635789', '123@gmail.com');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_productos` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `precio` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `fecha_caducidad` datetime NOT NULL,
  `codigo_barra` varchar(200) NOT NULL,
  `categoria_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_productos`, `nombre`, `precio`, `stock`, `fecha_caducidad`, `codigo_barra`, `categoria_id`) VALUES
(1, 'Mckay Alteza Bocado', 1200, 2, '2024-11-22 12:33:00', '7802230082503', 31),
(2, 'Dindon', 800, 5, '2025-01-10 12:35:00', '7802215502514', 63),
(3, 'Milo Galletas', 1000, 4, '2024-12-20 12:39:00', '8445291025417', 33),
(4, 'Mckay Alteza Frutilla', 1200, 3, '2025-01-11 12:40:00', '7802230082527', 31),
(5, 'Serranita', 350, 4, '2025-04-19 12:41:00', '7802408015241', 34),
(6, 'Alfajor Triple', 800, 19, '2025-05-16 12:43:00', '7790040425446', 36),
(7, 'Leche Colun en polvo', 1500, 8, '2025-05-10 12:47:00', '7802920003023', 38),
(8, 'Carioca', 350, 5, '2025-03-07 12:48:00', '7802408015081', 34),
(9, 'Galleta Toddy', 1300, 23, '2025-02-08 12:50:00', '7500478008780', 39),
(10, 'Gran Cereal Clasica', 1100, 23, '2025-02-20 12:51:00', '7802215302053', 32),
(11, 'Full', 200, 20, '2025-05-21 12:53:00', '7802200133426', 41),
(12, 'Galleta Oreo', 550, 32, '2025-02-07 12:56:00', '7590011251100', 43),
(13, 'Brownie Chips', 700, 10, '2025-03-07 12:57:00', '7803525999544', 45),
(14, 'Mckay vino', 1000, 25, '2025-03-08 12:58:00', '7613032443191', 31),
(15, 'Yogu yogu', 700, 15, '2024-11-13 13:01:00', '7802910301207', 46),
(16, 'Jugo Watts', 500, 29, '2024-11-14 13:03:00', '7802810006400', 48),
(17, 'Mckay Kuky', 1100, 19, '2025-03-14 13:04:00', '7802230081162', 31),
(18, 'Bocaditos Membrillo', 1400, 10, '2025-03-08 13:06:00', '7802225630900', 49),
(19, 'ColaCao Balls', 2400, 10, '2025-03-07 13:09:00', '7802420009884', 51),
(20, 'Galleta Ricochoc Clasico', 1600, 12, '2025-03-12 13:10:00', '7802225640558', 49),
(21, 'Milo en Polvo', 2500, 7, '2025-01-03 13:13:00', '7613030447979', 53),
(22, 'Oblea dos en uno', 1100, 9, '2025-03-07 13:15:00', '7896058258462', 54),
(23, 'Galleta Soda', 450, 20, '2025-02-08 13:16:00', '7802215511615', 32),
(24, 'Galleta Bon o Bon', 1400, 12, '2025-03-14 13:17:00', '7802225640770', 55),
(25, 'Galleta Limon', 1000, 14, '2025-03-08 13:18:00', '7802215505270', 32),
(26, 'Pastafrola Membrillo', 850, 13, '2025-02-07 13:19:00', '7798094229768', 36),
(27, 'Crackelet', 900, 13, '2025-03-07 13:20:00', '7802215511011', 32),
(28, 'Menta Eucaliptus', 300, 20, '2025-03-22 13:21:00', '7802408001091', 57),
(29, 'Mckay Maravilla', 1000, 32, '2025-02-14 13:22:00', '7613039496275', 31),
(30, 'Galletas Donuts', 1200, 15, '2024-12-13 13:23:00', '7802215508523', 32),
(31, 'Oblea Frutilla', 1100, 10, '2025-03-07 13:25:00', '7896058258479', 54),
(32, 'Frac Clasica', 1100, 25, '2025-02-14 13:26:00', '7802215512391', 32),
(33, 'Nik Bocado', 800, 10, '2025-03-07 13:27:00', '7802215504655', 32),
(34, 'M&M Chocolate', 1400, 16, '2025-02-06 13:28:00', '040000536819', 58),
(35, 'Alfajor Blanco', 650, 17, '2025-02-07 13:30:00', '7790040613607', 55),
(36, 'Oblea Bocado', 1000, 14, '2025-01-11 13:30:00', '7896058258455', 54),
(37, 'Leche Chocolate', 550, 15, '2024-11-11 13:32:00', '7802910054202', 59),
(38, 'Gretel Chocolate', 1200, 12, '2025-03-22 13:33:00', '7802215515019', 32),
(39, 'Turron de Mani', 500, 9, '2025-04-25 13:34:00', '7802225260657', 54),
(40, 'Mani Tifany\'s', 350, 11, '2025-06-13 13:35:00', '7802225427289', 63),
(41, 'Vizzio Chocolate', 700, 11, '2025-03-14 13:36:00', '7802215105913', 64),
(42, 'Frac Chocolate Frutilla', 1100, 16, '2025-05-07 13:37:00', '7802215512377', 32),
(43, 'ObaOba', 450, 28, '2025-05-15 13:38:00', '7802225281614', 65),
(44, 'Obsesion', 1100, 15, '2025-04-12 13:39:00', '7802215505027', 32),
(45, 'Yogu Mora', 500, 20, '2024-11-09 13:39:00', '7802910303201', 46),
(46, 'Leche Vainilla', 500, 20, '2025-05-08 13:40:00', '7802920106106', 38),
(47, 'Bonbon', 250, 70, '2025-08-21 13:41:00', '78023994', 55),
(48, 'Nikolo', 350, 26, '2024-12-13 13:43:00', '7802225538114', 54),
(49, 'Frac Naranja', 1000, 23, '2025-03-16 13:44:00', '7802215512414', 32),
(50, 'Frac Capuchino', 1000, 20, '2025-04-18 13:45:00', '7613039257334', 32),
(52, 'Choco Chipss', 1100, 20, '2024-11-30 19:49:00', '8476537409876', 43),
(53, 'Express Pepsi', 350, 23, '2024-11-30 00:25:00', '7801620003845', 68);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos_has_proveedor`
--

CREATE TABLE `productos_has_proveedor` (
  `productos_id` int(11) NOT NULL,
  `proveedor_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `productos_has_proveedor`
--

INSERT INTO `productos_has_proveedor` (`productos_id`, `proveedor_id`) VALUES
(1, 3),
(2, 4),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 3),
(13, 1),
(14, 1),
(15, 1),
(16, 1),
(17, 1),
(18, 1),
(19, 1),
(20, 1),
(21, 1),
(22, 1),
(23, 1),
(24, 3),
(25, 1),
(26, 1),
(27, 1),
(28, 1),
(29, 1),
(30, 1),
(31, 1),
(32, 1),
(33, 1),
(34, 1),
(35, 1),
(36, 1),
(37, 1),
(38, 1),
(39, 1),
(40, 1),
(41, 1),
(42, 1),
(43, 1),
(44, 1),
(45, 1),
(46, 1),
(47, 1),
(48, 1),
(49, 1),
(50, 1),
(52, 3),
(53, 6);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto_control`
--

CREATE TABLE `producto_control` (
  `id_control` int(11) NOT NULL,
  `id_productos` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `precio` int(11) NOT NULL,
  `stock` int(11) NOT NULL,
  `fecha_caducidad` datetime NOT NULL,
  `estado` enum('activo','caducado','merma') DEFAULT 'activo',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `producto_control`
--

INSERT INTO `producto_control` (`id_control`, `id_productos`, `nombre`, `precio`, `stock`, `fecha_caducidad`, `estado`, `fecha_registro`) VALUES
(297, 15, 'Yogu yogu', 700, 15, '2024-11-13 13:01:00', 'caducado', '2024-11-10 16:09:27'),
(298, 16, 'Jugo Watts', 500, 29, '2024-11-14 13:03:00', 'caducado', '2024-11-10 16:09:27'),
(301, 37, 'Leche Chocolate', 550, 15, '2024-11-11 13:32:00', 'caducado', '2024-11-10 17:08:11'),
(302, 45, 'Yogu Mora', 500, 20, '2024-11-09 13:39:00', 'caducado', '2024-11-10 17:08:11'),
(897, 1, 'Mckay Alteza Bocado', 1200, 5, '2024-11-22 12:33:00', 'caducado', '2024-11-21 14:43:57');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `id_proveedor` int(11) NOT NULL,
  `nombre_proveedor` varchar(200) NOT NULL,
  `numero_movil` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id_proveedor`, `nombre_proveedor`, `numero_movil`) VALUES
(1, 'Nestle', '299243433'),
(2, 'Dany Boys', '983746534'),
(3, 'Bidfood', '746352637'),
(4, 'Dimak', '987463545'),
(5, 'Espol', '876350983'),
(6, 'Ccu', '876351097');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuarios` int(11) NOT NULL,
  `user` varchar(45) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `tipo_empleado` varchar(45) NOT NULL,
  `empleado_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuarios`, `user`, `pass`, `tipo_empleado`, `empleado_id`) VALUES
(1, 'admin', '$2y$10$msHkPSroQEAZoTV.ovRcAu7M7LaogFVNgQkxeQGrGMWdZevMda.U2', 'Administrador', 1),
(2, 'vendedor', '$2y$10$xbWnRk9w0t3EsUlQsz/K7uBx4mz8oGGCQUd.Syr2RVxvopoAvYMva', 'Vendedor', 2),
(3, 'vende', '$2y$10$MdUrxveHCCrqeScvcU1fOORShC17AQpp6lOm9bK2yvuEtCgXG5lre', 'Vendedor', 2),
(4, 'Jp', '$2y$10$JI6mn0mamFSsJScFNvhwvObyjKHb/khXO9dWjjDErePx.jpVghMEa', 'Administrador', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta`
--

CREATE TABLE `venta` (
  `id_venta` int(11) NOT NULL,
  `total_venta` int(11) NOT NULL,
  `fecha_venta` datetime NOT NULL,
  `id_empleado` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `venta`
--

INSERT INTO `venta` (`id_venta`, `total_venta`, `fecha_venta`, `id_empleado`) VALUES
(1, 800, '2024-11-22 00:00:00', 1),
(2, 1200, '2024-11-29 01:17:00', 1),
(3, 1200, '2024-11-29 01:17:00', 1),
(4, 2350, '2024-11-12 01:49:00', 1),
(5, 2200, '2024-11-12 23:25:00', 1),
(6, 350, '2024-11-13 01:21:00', 1),
(7, 0, '2024-11-21 11:34:01', 1),
(8, 0, '2024-11-21 11:34:11', 1),
(9, 0, '2024-11-21 11:34:18', 1),
(10, 0, '2024-11-21 11:34:57', 1),
(11, 0, '2024-11-21 11:35:14', 2),
(12, 1200, '2024-11-21 11:35:50', 1),
(13, 1200, '2024-11-21 11:36:36', 1),
(26, 0, '2024-11-21 11:57:48', 1),
(27, 0, '2024-11-21 11:58:26', 1),
(28, 800, '2024-11-21 12:00:11', 1),
(29, 1500, '2024-11-21 12:00:46', 1),
(30, 2350, '2024-11-22 12:00:01', 1),
(31, 1200, '2024-11-22 14:03:48', 1),
(32, 1200, '2024-11-22 14:03:54', 2),
(33, 1200, '2024-11-22 14:05:01', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `venta_productos`
--

CREATE TABLE `venta_productos` (
  `venta_id` int(11) NOT NULL,
  `productos_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `venta_productos`
--

INSERT INTO `venta_productos` (`venta_id`, `productos_id`, `cantidad`, `precio_unitario`) VALUES
(1, 2, 1, 800),
(2, 1, 1, 1200),
(3, 1, 1, 1200),
(4, 4, 1, 1200),
(4, 5, 1, 350),
(4, 6, 1, 800),
(5, 1, 1, 1200),
(5, 3, 1, 1000),
(6, 53, 1, 350),
(12, 1, 1, 1200),
(13, 1, 1, 1200),
(28, 2, 1, 800),
(29, 2, 1, 800),
(29, 5, 2, 350),
(30, 2, 1, 800),
(30, 4, 1, 1200),
(30, 5, 1, 350),
(31, 1, 1, 1200),
(32, 1, 1, 1200),
(33, 1, 1, 1200);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`),
  ADD KEY `fk_categoria_categoria` (`categoria_padre_id`);

--
-- Indices de la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD PRIMARY KEY (`id_empleado`),
  ADD KEY `fk_empleado_local` (`local_id`);

--
-- Indices de la tabla `historial_movimientos`
--
ALTER TABLE `historial_movimientos`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `local`
--
ALTER TABLE `local`
  ADD PRIMARY KEY (`id_local`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_productos`),
  ADD KEY `fk_productos_categoria` (`categoria_id`);

--
-- Indices de la tabla `productos_has_proveedor`
--
ALTER TABLE `productos_has_proveedor`
  ADD PRIMARY KEY (`productos_id`,`proveedor_id`),
  ADD KEY `fk_proveedor_productos` (`proveedor_id`);

--
-- Indices de la tabla `producto_control`
--
ALTER TABLE `producto_control`
  ADD PRIMARY KEY (`id_control`),
  ADD UNIQUE KEY `idx_id_productos` (`id_productos`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_proveedor`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuarios`),
  ADD KEY `fk_usuarios_empleado` (`empleado_id`);

--
-- Indices de la tabla `venta`
--
ALTER TABLE `venta`
  ADD PRIMARY KEY (`id_venta`),
  ADD KEY `fk_venta_empleado` (`id_empleado`);

--
-- Indices de la tabla `venta_productos`
--
ALTER TABLE `venta_productos`
  ADD PRIMARY KEY (`venta_id`,`productos_id`),
  ADD KEY `fk_venta_productos_productos` (`productos_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT de la tabla `empleado`
--
ALTER TABLE `empleado`
  MODIFY `id_empleado` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `historial_movimientos`
--
ALTER TABLE `historial_movimientos`
  MODIFY `id_movimiento` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_productos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT de la tabla `producto_control`
--
ALTER TABLE `producto_control`
  MODIFY `id_control` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1007;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuarios` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `venta`
--
ALTER TABLE `venta`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD CONSTRAINT `fk_categoria_categoria` FOREIGN KEY (`categoria_padre_id`) REFERENCES `categoria` (`id_categoria`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_categoria_padre` FOREIGN KEY (`categoria_padre_id`) REFERENCES `categoria` (`id_categoria`) ON DELETE CASCADE;

--
-- Filtros para la tabla `empleado`
--
ALTER TABLE `empleado`
  ADD CONSTRAINT `fk_empleado_local` FOREIGN KEY (`local_id`) REFERENCES `local` (`id_local`) ON DELETE CASCADE;

--
-- Filtros para la tabla `historial_movimientos`
--
ALTER TABLE `historial_movimientos`
  ADD CONSTRAINT `historial_movimientos_ibfk_1` FOREIGN KEY (`id_producto`) REFERENCES `productos` (`id_productos`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_categoria` FOREIGN KEY (`categoria_id`) REFERENCES `categoria` (`id_categoria`) ON DELETE CASCADE;

--
-- Filtros para la tabla `productos_has_proveedor`
--
ALTER TABLE `productos_has_proveedor`
  ADD CONSTRAINT `fk_productos_proveedor` FOREIGN KEY (`productos_id`) REFERENCES `productos` (`id_productos`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_proveedor_productos` FOREIGN KEY (`proveedor_id`) REFERENCES `proveedor` (`id_proveedor`);

--
-- Filtros para la tabla `producto_control`
--
ALTER TABLE `producto_control`
  ADD CONSTRAINT `producto_control_ibfk_1` FOREIGN KEY (`id_productos`) REFERENCES `productos` (`id_productos`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_empleado` FOREIGN KEY (`empleado_id`) REFERENCES `empleado` (`id_empleado`);

--
-- Filtros para la tabla `venta`
--
ALTER TABLE `venta`
  ADD CONSTRAINT `fk_venta_empleado` FOREIGN KEY (`id_empleado`) REFERENCES `empleado` (`id_empleado`);

--
-- Filtros para la tabla `venta_productos`
--
ALTER TABLE `venta_productos`
  ADD CONSTRAINT `fk_venta_productos_productos` FOREIGN KEY (`productos_id`) REFERENCES `productos` (`id_productos`),
  ADD CONSTRAINT `fk_venta_productos_venta` FOREIGN KEY (`venta_id`) REFERENCES `venta` (`id_venta`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
