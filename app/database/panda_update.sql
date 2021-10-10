-- --------------------------------------------------------
-- Servidor:                     localhost
-- Versão do servidor:           10.3.28-MariaDB-1:10.3.28+maria~focal - mariadb.org binary distribution
-- OS do Servidor:               debian-linux-gnu
-- HeidiSQL Versão:              11.2.0.6213
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Copiando dados para a tabela panda.brand: ~4 rows (aproximadamente)
TRUNCATE `brand`;
/*!40000 ALTER TABLE `brand` DISABLE KEYS */;
INSERT INTO `brand` (`id`, `system_user_id`, `name`, `status`, `created_at`, `updated_at`) VALUES
	(1, 4, 'MARMITA DA LORA', '1', '2021-08-10 20:30:26', '2021-08-10 20:30:26'),
	(2, 1, 'APPLE', '1', '2021-08-11 01:30:43', '2021-08-11 01:30:43'),
	(3, 3, 'IPHONE', '1', '2021-08-12 13:30:05', '2021-08-12 13:30:05'),
	(4, 5, 'POUSADA SANTA RITA', '1', '2021-08-22 17:46:12', '2021-08-22 17:46:12');
/*!40000 ALTER TABLE `brand` ENABLE KEYS */;

-- Copiando dados para a tabela panda.employee: ~0 rows (aproximadamente)
TRUNCATE `employee`;
/*!40000 ALTER TABLE `employee` DISABLE KEYS */;
INSERT INTO `employee` (`id`, `system_user_id`, `name`, `document`, `contact`, `salary`, `created_at`, `updated_at`) VALUES
	(1, 1, 'DAYRON', '022.565.423-78', '(99) 84536-3274', 1800.00, '2021-08-09 00:00:00', '2021-08-09 03:17:12');
/*!40000 ALTER TABLE `employee` ENABLE KEYS */;

-- Copiando dados para a tabela panda.exes: ~2 rows (aproximadamente)
TRUNCATE `exes`;
/*!40000 ALTER TABLE `exes` DISABLE KEYS */;
INSERT INTO `exes` (`id`, `system_user_id`, `inventory_id`, `description`, `amount`, `price`, `created_at`, `updated_at`) VALUES
	(1, 1, NULL, 'GASOLINA ONIX', NULL, 100.00, NULL, NULL),
	(2, 1, NULL, 'CREDITO CEL', NULL, 12.00, NULL, NULL),
	(3, 5, 12, 'FULANO PEGOU', '2', 3.00, '2021-08-17 00:00:00', '2021-08-30 18:51:33'),
	(4, 5, NULL, 'FORNECEDOR DE CAPINHA', NULL, 33.22, '2021-08-18 00:00:00', '2021-08-30 19:14:15');
/*!40000 ALTER TABLE `exes` ENABLE KEYS */;

-- Copiando dados para a tabela panda.inventory: ~7 rows (aproximadamente)
TRUNCATE `inventory`;
/*!40000 ALTER TABLE `inventory` DISABLE KEYS */;
INSERT INTO `inventory` (`id`, `system_user_id`, `product_id`, `amount`, `amount_available`, `price`, `final_price`, `status`, `created_at`, `updated_at`) VALUES
	(6, 4, 4, 0, NULL, 12, 44, '0', '2021-08-10 20:34:37', '2021-08-27 04:05:32'),
	(7, 3, 5, 5, 5, 0.02, 0.04, '0', '2021-08-19 19:25:14', '2021-09-13 02:58:25'),
	(11, 5, 7, 5, NULL, 0, 10, '0', '2021-08-27 03:13:21', '2021-08-30 23:25:11'),
	(12, 5, 8, 5, 4, 1.23, 2, '1', '2021-08-29 17:27:05', '2021-09-04 00:23:53'),
	(13, 5, 8, 2, 2, 1.1, 2, '1', '2021-08-30 23:32:42', '2021-09-04 00:30:14'),
	(14, 4, 6, NULL, NULL, 1, 12, '1', '2021-09-04 19:08:28', '2021-09-04 19:10:28'),
	(15, 4, 6, NULL, NULL, 1.2, 12, '1', '2021-09-04 19:10:44', '2021-09-04 19:10:44'),
	(16, 4, 6, 34, 34, 1.2, 12.33, '1', '2021-09-04 19:12:10', '2021-09-04 19:12:10');
/*!40000 ALTER TABLE `inventory` ENABLE KEYS */;

-- Copiando dados para a tabela panda.office: ~0 rows (aproximadamente)
TRUNCATE `office`;
/*!40000 ALTER TABLE `office` DISABLE KEYS */;
INSERT INTO `office` (`id`, `system_user_id`, `description`, `price`, `created_at`, `updated_at`, `office_type_id`) VALUES
	(1, 1, 'IPHONE 7 DO MARQUIN', 180, '2021-08-08 17:38:42', '2021-08-08 17:38:42', 1);
/*!40000 ALTER TABLE `office` ENABLE KEYS */;

-- Copiando dados para a tabela panda.office_type: ~0 rows (aproximadamente)
TRUNCATE `office_type`;
/*!40000 ALTER TABLE `office_type` DISABLE KEYS */;
INSERT INTO `office_type` (`id`, `system_user_id`, `name`, `status`, `created_at`, `updated_at`) VALUES
	(1, 1, 'MANUTENÇÃO', '1', '2021-08-08 17:37:54', '2021-08-08 17:37:54');
/*!40000 ALTER TABLE `office_type` ENABLE KEYS */;

-- Copiando dados para a tabela panda.payable: ~2 rows (aproximadamente)
TRUNCATE `payable`;
/*!40000 ALTER TABLE `payable` DISABLE KEYS */;
INSERT INTO `payable` (`id`, `system_user_id`, `description`, `price`, `status`, `created_at`, `updated_at`) VALUES
	(1, 1, 'FORNECEDOR DE CAPINHA', 1402.22, '0', '2021-08-09 02:02:07', '2021-08-09 02:10:13'),
	(2, 1, 'FORNECEDOR DE CAPINHA', 2333.22, '0', '2021-12-30 00:00:00', '2021-08-09 02:11:25'),
	(3, 5, 'CONTA A PAGAR TESTE', 23.00, '0', '2021-08-25 00:00:00', '2021-08-25 22:42:18');
/*!40000 ALTER TABLE `payable` ENABLE KEYS */;

-- Copiando dados para a tabela panda.product: ~2 rows (aproximadamente)
TRUNCATE `product`;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` (`id`, `system_user_id`, `brand_id`, `sku`, `name`, `alias`, `status`, `image`, `created_at`, `updated_at`) VALUES
	(4, 4, 1, 'XABL2X9E', 'MARMITA DA LORA', 'MARMITA', '1', 'b2827f51-01e9-4b87-a004-74054f9a.jpeg', '2021-08-10 20:33:56', '2021-08-10 20:33:56'),
	(5, 3, 3, '1TWXWAPI', 'CAPINHA IPHONE 7', 'CAPA IPHONE 7', '1', NULL, '2021-08-19 19:24:58', '2021-08-19 19:24:58'),
	(6, 4, 1, 'FWM2BI1S', 'TESTE', 'TESTE', '1', NULL, '2021-08-22 16:00:56', '2021-08-22 16:00:56'),
	(7, 5, 4, 'S9E0YKIF', 'QUARTO', 'QUARTO', '1', NULL, '2021-08-22 17:46:39', '2021-08-22 18:18:02'),
	(8, 5, 4, 'HQUALSGY', 'COCA-COLA', 'COCA', '1', NULL, '2021-08-29 17:26:44', '2021-08-29 17:26:44');
/*!40000 ALTER TABLE `product` ENABLE KEYS */;

-- Copiando dados para a tabela panda.provider: ~0 rows (aproximadamente)
TRUNCATE `provider`;
/*!40000 ALTER TABLE `provider` DISABLE KEYS */;
/*!40000 ALTER TABLE `provider` ENABLE KEYS */;

-- Copiando dados para a tabela panda.sale: ~0 rows (aproximadamente)
TRUNCATE `sale`;
/*!40000 ALTER TABLE `sale` DISABLE KEYS */;
/*!40000 ALTER TABLE `sale` ENABLE KEYS */;

-- Copiando dados para a tabela panda.sale_inventory: ~0 rows (aproximadamente)
TRUNCATE `sale_inventory`;
/*!40000 ALTER TABLE `sale_inventory` DISABLE KEYS */;
/*!40000 ALTER TABLE `sale_inventory` ENABLE KEYS */;

-- Copiando dados para a tabela panda.sale_type: ~0 rows (aproximadamente)
TRUNCATE `sale_type`;
/*!40000 ALTER TABLE `sale_type` DISABLE KEYS */;
INSERT INTO `sale_type` (`id`, `system_user_id`, `name`, `status`, `created_at`, `updated_at`) VALUES
	(1, 3, 'CAPINHA IPHONE 7', '1', '2021-08-19 19:06:50', '2021-08-19 19:06:50'),
	(2, 4, 'TESTE', '1', '2021-08-21 23:22:09', '2021-08-21 23:22:09'),
	(3, 5, 'PIX', '1', '2021-08-22 17:47:34', '2021-08-22 17:47:34');
/*!40000 ALTER TABLE `sale_type` ENABLE KEYS */;

-- Copiando dados para a tabela panda.system_document: ~0 rows (aproximadamente)
TRUNCATE `system_document`;
/*!40000 ALTER TABLE `system_document` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_document` ENABLE KEYS */;

-- Copiando dados para a tabela panda.system_document_category: ~0 rows (aproximadamente)
TRUNCATE `system_document_category`;
/*!40000 ALTER TABLE `system_document_category` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_document_category` ENABLE KEYS */;

-- Copiando dados para a tabela panda.system_document_group: ~0 rows (aproximadamente)
TRUNCATE `system_document_group`;
/*!40000 ALTER TABLE `system_document_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_document_group` ENABLE KEYS */;

-- Copiando dados para a tabela panda.system_document_user: ~0 rows (aproximadamente)
TRUNCATE `system_document_user`;
/*!40000 ALTER TABLE `system_document_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_document_user` ENABLE KEYS */;

-- Copiando dados para a tabela panda.system_group: ~2 rows (aproximadamente)
TRUNCATE `system_group`;
/*!40000 ALTER TABLE `system_group` DISABLE KEYS */;
INSERT INTO `system_group` (`id`, `name`) VALUES
	(1, 'Admin'),
	(2, 'Assistencia Tecnica'),
	(3, 'Restaurante'),
	(4, 'Hotel');
/*!40000 ALTER TABLE `system_group` ENABLE KEYS */;

-- Copiando dados para a tabela panda.system_group_program: ~106 rows (aproximadamente)
TRUNCATE `system_group_program`;
/*!40000 ALTER TABLE `system_group_program` DISABLE KEYS */;
INSERT INTO `system_group_program` (`id`, `system_group_id`, `system_program_id`) VALUES
	(1, 1, 1),
	(2, 1, 2),
	(3, 1, 3),
	(4, 1, 4),
	(5, 1, 5),
	(6, 1, 6),
	(7, 1, 8),
	(8, 1, 9),
	(9, 1, 11),
	(10, 1, 14),
	(11, 1, 15),
	(20, 1, 21),
	(25, 1, 26),
	(26, 1, 27),
	(27, 1, 28),
	(28, 1, 29),
	(30, 1, 31),
	(31, 1, 32),
	(32, 1, 33),
	(33, 1, 34),
	(34, 1, 35),
	(36, 1, 36),
	(37, 1, 37),
	(38, 1, 38),
	(39, 1, 39),
	(40, 1, 40),
	(77, 2, 10),
	(78, 2, 12),
	(79, 2, 13),
	(80, 2, 16),
	(81, 2, 17),
	(82, 2, 18),
	(83, 2, 19),
	(84, 2, 20),
	(85, 2, 22),
	(86, 2, 23),
	(87, 2, 24),
	(88, 2, 25),
	(89, 2, 30),
	(134, 2, 41),
	(135, 2, 42),
	(136, 2, 43),
	(137, 2, 44),
	(138, 2, 45),
	(139, 2, 46),
	(140, 2, 47),
	(141, 2, 48),
	(142, 2, 49),
	(143, 2, 50),
	(144, 2, 51),
	(145, 2, 52),
	(146, 2, 53),
	(147, 2, 54),
	(148, 2, 55),
	(149, 2, 56),
	(150, 2, 57),
	(151, 2, 58),
	(152, 2, 59),
	(153, 3, 10),
	(154, 3, 12),
	(155, 3, 13),
	(156, 3, 16),
	(157, 3, 17),
	(158, 3, 18),
	(159, 3, 19),
	(160, 3, 20),
	(161, 3, 22),
	(162, 3, 23),
	(163, 3, 24),
	(164, 3, 25),
	(165, 3, 30),
	(166, 3, 60),
	(167, 3, 61),
	(168, 3, 62),
	(169, 3, 63),
	(170, 3, 64),
	(171, 3, 65),
	(172, 3, 66),
	(173, 3, 67),
	(174, 3, 68),
	(175, 3, 69),
	(176, 3, 70),
	(177, 3, 71),
	(178, 3, 72),
	(179, 3, 73),
	(180, 3, 74),
	(181, 3, 75),
	(182, 3, 76),
	(183, 3, 77),
	(184, 3, 78),
	(185, 2, 79),
	(186, 2, 80),
	(187, 3, 81),
	(188, 3, 82),
	(189, 4, 83),
	(190, 4, 84),
	(191, 4, 85),
	(192, 4, 86),
	(193, 4, 87),
	(194, 4, 88),
	(195, 4, 89),
	(196, 4, 90),
	(197, 4, 91),
	(198, 4, 92),
	(199, 4, 93),
	(200, 4, 94),
	(201, 4, 95),
	(202, 4, 96),
	(203, 4, 97),
	(204, 4, 98),
	(205, 4, 99),
	(206, 4, 100),
	(207, 4, 101),
	(208, 4, 102),
	(209, 4, 103);
/*!40000 ALTER TABLE `system_group_program` ENABLE KEYS */;

-- Copiando dados para a tabela panda.system_message: ~0 rows (aproximadamente)
TRUNCATE `system_message`;
/*!40000 ALTER TABLE `system_message` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_message` ENABLE KEYS */;

-- Copiando dados para a tabela panda.system_notification: ~0 rows (aproximadamente)
TRUNCATE `system_notification`;
/*!40000 ALTER TABLE `system_notification` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_notification` ENABLE KEYS */;

-- Copiando dados para a tabela panda.system_preference: ~0 rows (aproximadamente)
TRUNCATE `system_preference`;
/*!40000 ALTER TABLE `system_preference` DISABLE KEYS */;
/*!40000 ALTER TABLE `system_preference` ENABLE KEYS */;

-- Copiando dados para a tabela panda.system_program: ~92 rows (aproximadamente)
TRUNCATE `system_program`;
/*!40000 ALTER TABLE `system_program` DISABLE KEYS */;
INSERT INTO `system_program` (`id`, `name`, `controller`) VALUES
	(1, 'System Group Form', 'SystemGroupForm'),
	(2, 'System Group List', 'SystemGroupList'),
	(3, 'System Program Form', 'SystemProgramForm'),
	(4, 'System Program List', 'SystemProgramList'),
	(5, 'System User Form', 'SystemUserForm'),
	(6, 'System User List', 'SystemUserList'),
	(7, 'Common Page', 'CommonPage'),
	(8, 'System PHP Info', 'SystemPHPInfoView'),
	(9, 'System ChangeLog View', 'SystemChangeLogView'),
	(10, 'Welcome View', 'WelcomeView'),
	(11, 'System Sql Log', 'SystemSqlLogList'),
	(12, 'System Profile View', 'SystemProfileView'),
	(13, 'System Profile Form', 'SystemProfileForm'),
	(14, 'System SQL Panel', 'SystemSQLPanel'),
	(15, 'System Access Log', 'SystemAccessLogList'),
	(16, 'System Message Form', 'SystemMessageForm'),
	(17, 'System Message List', 'SystemMessageList'),
	(18, 'System Message Form View', 'SystemMessageFormView'),
	(19, 'System Notification List', 'SystemNotificationList'),
	(20, 'System Notification Form View', 'SystemNotificationFormView'),
	(21, 'System Document Category List', 'SystemDocumentCategoryFormList'),
	(22, 'System Document Form', 'SystemDocumentForm'),
	(23, 'System Document Upload Form', 'SystemDocumentUploadForm'),
	(24, 'System Document List', 'SystemDocumentList'),
	(25, 'System Shared Document List', 'SystemSharedDocumentList'),
	(26, 'System Unit Form', 'SystemUnitForm'),
	(27, 'System Unit List', 'SystemUnitList'),
	(28, 'System Access stats', 'SystemAccessLogStats'),
	(29, 'System Preference form', 'SystemPreferenceForm'),
	(30, 'System Support form', 'SystemSupportForm'),
	(31, 'System PHP Error', 'SystemPHPErrorLogView'),
	(32, 'System Database Browser', 'SystemDatabaseExplorer'),
	(33, 'System Table List', 'SystemTableList'),
	(34, 'System Data Browser', 'SystemDataBrowser'),
	(35, 'System Menu Editor', 'SystemMenuEditor'),
	(36, 'System Request Log', 'SystemRequestLogList'),
	(37, 'System Request Log View', 'SystemRequestLogView'),
	(38, 'System Administration Dashboard', 'SystemAdministrationDashboard'),
	(39, 'System Log Dashboard', 'SystemLogDashboard'),
	(40, 'System Session dump', 'SystemSessionDumpView'),
	(41, 'Assistence Brand Form', 'AssistenceBrandForm'),
	(42, 'Assistence Brand List', 'AssistenceBrandList'),
	(43, 'Assistence Dashboard', 'AssistenceDashboard'),
	(44, 'Assistence Employee Form', 'AssistenceEmployeeForm'),
	(45, 'Assistence Employee List', 'AssistenceEmployeeList'),
	(46, 'Assistence Exes Form', 'AssistenceExesForm'),
	(47, 'Assistence Exes List', 'AssistenceExesList'),
	(48, 'Assistence Inventory Form', 'AssistenceInventoryForm'),
	(49, 'Assistence Inventory List', 'AssistenceInventoryList'),
	(50, 'Assistence Office Form', 'AssistenceOfficeForm'),
	(51, 'Assistence Office List', 'AssistenceOfficeList'),
	(52, 'Assistence Office Type Form List', 'AssistenceOfficeTypeFormList'),
	(53, 'Assistence Payable Form', 'AssistencePayableForm'),
	(54, 'Assistence Payable List', 'AssistencePayableList'),
	(55, 'Assistence Product Form', 'AssistenceProductForm'),
	(56, 'Assistence Product List', 'AssistenceProductList'),
	(57, 'Assistence Sale Form', 'AssistenceSaleForm'),
	(58, 'Assistence Sale List', 'AssistenceSaleList'),
	(59, 'Assistence Sale Type Form List', 'AssistenceSaleTypeFormList'),
	(60, 'Restaurant Brand Form', 'RestaurantBrandForm'),
	(61, 'Restaurant Brand List', 'RestaurantBrandList'),
	(62, 'Restaurant Dashboard', 'RestaurantDashboard'),
	(63, 'Restaurant Employee Form', 'RestaurantEmployeeForm'),
	(64, 'Restaurant Employee List', 'RestaurantEmployeeList'),
	(65, 'Restaurant Exes Form', 'RestaurantExesForm'),
	(66, 'Restaurant Exes List', 'RestaurantExesList'),
	(67, 'Restaurant Inventory Form', 'RestaurantInventoryForm'),
	(68, 'Restaurant Inventory List', 'RestaurantInventoryList'),
	(69, 'Restaurant Office Form', 'RestaurantOfficeForm'),
	(70, 'Restaurant Office List', 'RestaurantOfficeList'),
	(71, 'Restaurant Office Type Form List', 'RestaurantOfficeTypeFormList'),
	(72, 'Restaurant Payable Form', 'RestaurantPayableForm'),
	(73, 'Restaurant Payable List', 'RestaurantPayableList'),
	(74, 'Restaurant Product Form', 'RestaurantProductForm'),
	(75, 'Restaurant Product List', 'RestaurantProductList'),
	(76, 'Restaurant Sale Form', 'RestaurantSaleForm'),
	(77, 'Restaurant Sale List', 'RestaurantSaleList'),
	(78, 'Restaurant Sale Type Form List', 'RestaurantSaleTypeFormList'),
	(79, 'Assistence Provider Form', 'AssistenceProviderForm'),
	(80, 'Assistence Provider List', 'AssistenceProviderList'),
	(81, 'Restaurant Provider Form', 'RestaurantProviderForm'),
	(82, 'Restaurant Provider List', 'RestaurantProviderList'),
	(83, 'Roost Brand Form', 'RoostBrandForm'),
	(84, 'Roost Brand List', 'RoostBrandList'),
	(85, 'Roost Dashboard', 'RoostDashboard'),
	(86, 'Roost Employee Form', 'RoostEmployeeForm'),
	(87, 'Roost Employee List', 'RoostEmployeeList'),
	(88, 'Roost Exes Form', 'RoostExesForm'),
	(89, 'Roost Exes List', 'RoostExesList'),
	(90, 'Roost Inventory Form', 'RoostInventoryForm'),
	(91, 'Roost Inventory List', 'RoostInventoryList'),
	(92, 'Roost Office Form', 'RoostOfficeForm'),
	(93, 'Roost Office List', 'RoostOfficeList'),
	(94, 'Roost Office Type Form List', 'RoostOfficeTypeFormList'),
	(95, 'Roost Payable Form', 'RoostPayableForm'),
	(96, 'Roost Payable List', 'RoostPayableList'),
	(97, 'Roost Product Form', 'RoostProductForm'),
	(98, 'Roost Product List', 'RoostProductList'),
	(99, 'Roost Provider Form', 'RoostProviderForm'),
	(100, 'Roost Provider List', 'RoostProviderList'),
	(101, 'Roost Sale Form', 'RoostSaleForm'),
	(102, 'Roost Sale List', 'RoostSaleList'),
	(103, 'Roost Sale Type Form List', 'RoostSaleTypeFormList');
/*!40000 ALTER TABLE `system_program` ENABLE KEYS */;

-- Copiando dados para a tabela panda.system_unit: ~4 rows (aproximadamente)
TRUNCATE `system_unit`;
/*!40000 ALTER TABLE `system_unit` DISABLE KEYS */;
INSERT INTO `system_unit` (`id`, `name`, `connection_name`) VALUES
	(3, 'Dcell', 'app'),
	(4, 'Toca da loira', 'app'),
	(5, 'Ps Rita', 'app');
/*!40000 ALTER TABLE `system_unit` ENABLE KEYS */;

-- Copiando dados para a tabela panda.system_user: ~6 rows (aproximadamente)
TRUNCATE `system_user`;
/*!40000 ALTER TABLE `system_user` DISABLE KEYS */;
INSERT INTO `system_user` (`id`, `name`, `login`, `password`, `email`, `frontpage_id`, `system_unit_id`, `active`, `type`) VALUES
	(1, 'Administrator', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'admin@admin.net', 38, 3, 'Y', 0),
	(2, 'User', 'user', 'ee11cbb19052e40b07aac0ca060c23ee', 'user@user.net', 10, NULL, 'Y', 0),
	(3, 'dcell', 'dcell', '202cb962ac59075b964b07152d234b70', 'tunele095@gmail.com', 43, 3, 'Y', 1),
	(4, 'Toca da Loira', 'tocadalora', '202cb962ac59075b964b07152d234b70', 'toca@gmail.com', 10, 4, 'Y', 1),
	(5, 'Hotel', 'hotel', '202cb962ac59075b964b07152d234b70', 'toca@gmail.com', 10, 5, 'Y', 1),
	(6, 'zaza', 'zaza', '202cb962ac59075b964b07152d234b70', 'zaza@zazaplay.com', 102, 5, 'Y', 3);
/*!40000 ALTER TABLE `system_user` ENABLE KEYS */;

-- Copiando dados para a tabela panda.system_user_group: ~5 rows (aproximadamente)
TRUNCATE `system_user_group`;
/*!40000 ALTER TABLE `system_user_group` DISABLE KEYS */;
INSERT INTO `system_user_group` (`id`, `system_user_id`, `system_group_id`) VALUES
	(1, 1, 1),
	(13, 2, 2),
	(15, 3, 2),
	(16, 5, 4),
	(17, 6, 4),
	(18, 4, 3);
/*!40000 ALTER TABLE `system_user_group` ENABLE KEYS */;

-- Copiando dados para a tabela panda.system_user_program: ~1 rows (aproximadamente)
TRUNCATE `system_user_program`;
/*!40000 ALTER TABLE `system_user_program` DISABLE KEYS */;
INSERT INTO `system_user_program` (`id`, `system_user_id`, `system_program_id`) VALUES
	(1, 2, 7);
/*!40000 ALTER TABLE `system_user_program` ENABLE KEYS */;

-- Copiando dados para a tabela panda.system_user_unit: ~5 rows (aproximadamente)
TRUNCATE `system_user_unit`;
/*!40000 ALTER TABLE `system_user_unit` DISABLE KEYS */;
INSERT INTO `system_user_unit` (`id`, `system_user_id`, `system_unit_id`) VALUES
	(1, 1, 3),
	(2, 3, 3),
	(4, 5, 5),
	(5, 6, 5),
	(6, 4, 4);
/*!40000 ALTER TABLE `system_user_unit` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
