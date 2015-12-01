-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Дек 01 2015 г., 09:51
-- Версия сервера: 5.5.46-0ubuntu0.14.04.2
-- Версия PHP: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `umcms`
--

-- --------------------------------------------------------

--
-- Структура таблицы `auth_assignment`
--

CREATE TABLE IF NOT EXISTS `auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('admin', '1', 1447426029),
('admin', '15', 1447661595),
('admin', '22', 1447663166),
('admin', '36', 1448011330),
('admin', '39', 1448372733),
('admin', '40', 1448376517),
('admin', '41', 1448376561),
('admin', '44', 1448434751),
('admin', '46', 1448434956),
('admin', '48', 1448435091),
('admin', '53', 1448436802),
('admin', '54', 1448437272),
('admin', '56', 1448453499),
('admin', '57', 1448520472),
('admin', '58', 1448520515),
('admin', '61', 1448954289),
('admin', '64', 1448955479),
('admin', '8', 1447944609),
('polzovatel', '37', 1448356341),
('polzovatel', '42', 1448011757),
('polzovatel', '59', 1448605963),
('polzovatel', '60', 1448611819),
('polzovatel', '62', 1448954448),
('polzovatel', '63', 1448954533);

-- --------------------------------------------------------

--
-- Структура таблицы `auth_item`
--

CREATE TABLE IF NOT EXISTS `auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` int(11) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `auth_item`
--

INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
('admin', 1, 'Админ', NULL, NULL, 1447335342, 1448616984),
('polzovatel', 1, 'Пользователь', NULL, NULL, NULL, 1448616932),
('rbac\\roles\\create', 2, 'Создание групп пользователей', NULL, NULL, 1448019035, 1448019035),
('rbac\\roles\\delete', 2, 'Удаление группы пользователей', NULL, NULL, 1448019035, 1448019035),
('rbac\\roles\\index', 2, 'Просмотр листинга групп пользователей', NULL, NULL, 1448019035, 1448019035),
('rbac\\roles\\update', 2, 'Редактирование группы пользователей', NULL, NULL, 1448019035, 1448019035),
('rbac\\roles\\view', 2, 'Просмотр группы пользователей', NULL, NULL, 1448019035, 1448019035),
('users/usercontroller/create', 2, 'Создание пользователей', NULL, NULL, 1448019002, 1448019002),
('users/usercontroller/delete', 2, 'Удаление пользователей', NULL, NULL, 1448019002, 1448019002),
('users/usercontroller/index', 2, 'Просмотр листинга пользователей', NULL, NULL, 1448019002, 1448019002),
('users/usercontroller/update', 2, 'Обновление пользователей', NULL, NULL, 1448019002, 1448019002),
('users/usercontroller/updatemyprofile', 2, 'Редактирование своего профиля', NULL, NULL, 1448616665, 1448616665),
('users/usercontroller/view', 2, 'Просмотр пользователя', NULL, NULL, 1448019002, 1448019002);

-- --------------------------------------------------------

--
-- Структура таблицы `auth_item_child`
--

CREATE TABLE IF NOT EXISTS `auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `auth_item_child`
--

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('admin', 'rbac\\roles\\create'),
('admin', 'rbac\\roles\\delete'),
('admin', 'rbac\\roles\\index'),
('admin', 'rbac\\roles\\update'),
('admin', 'rbac\\roles\\view'),
('admin', 'users/usercontroller/create'),
('admin', 'users/usercontroller/delete'),
('admin', 'users/usercontroller/index'),
('admin', 'users/usercontroller/update'),
('admin', 'users/usercontroller/updatemyprofile'),
('polzovatel', 'users/usercontroller/updatemyprofile'),
('admin', 'users/usercontroller/view');

-- --------------------------------------------------------

--
-- Структура таблицы `auth_rule`
--

CREATE TABLE IF NOT EXISTS `auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` text COLLATE utf8_unicode_ci,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `migration`
--

CREATE TABLE IF NOT EXISTS `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`version`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1447316619),
('m130524_201442_init', 1447316622),
('m140506_102106_rbac_init', 1447334562),
('m151106_072252_create_text_user', 1447316623),
('m151112_132320_rbac_init', 1447335342),
('m151113_113238_update_user_table', 1447415087),
('m151120_104918_create_rbac_permissions_list', 1448019036),
('m151123_061626_add_image_to_user', 1448259631),
('m151130_081732_create_uploads_table', 1448885250);

-- --------------------------------------------------------

--
-- Структура таблицы `product_category_module`
--

CREATE TABLE IF NOT EXISTS `product_category_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Название|text',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `product_module`
--

CREATE TABLE IF NOT EXISTS `product_module` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Название|text',
  `category_id` int(11) DEFAULT NULL COMMENT 'Категория|select',
  PRIMARY KEY (`id`),
  KEY `fk_product_category` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Структура таблицы `uploads`
--

CREATE TABLE IF NOT EXISTS `uploads` (
  `filepath` varchar(255) NOT NULL,
  `collection_id` int(11) NOT NULL,
  KEY `uploads_collection_uploads_FK` (`collection_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Дамп данных таблицы `uploads`
--

INSERT INTO `uploads` (`filepath`, `collection_id`) VALUES
('/upload/models/UploadsCollection/6/HuFcTlj9H-Y.jpg', 6),
('/upload/models/UploadsCollection/6/HuFcTlj9H-Y-1.jpg', 6),
('/upload/models/UploadsCollection/8/HuFcTlj9H-Y.jpg', 8),
('/upload/models/UploadsCollection/8/HuFcTlj9H-Y-1.jpg', 8),
('/upload/models/UploadsCollection/12/HuFcTlj9H-Y.jpg', 12),
('/upload/models/UploadsCollection/12/HuFcTlj9H-Y-1.jpg', 12);

-- --------------------------------------------------------

--
-- Структура таблицы `uploads_collection`
--

CREATE TABLE IF NOT EXISTS `uploads_collection` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `entity_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=13 ;

--
-- Дамп данных таблицы `uploads_collection`
--

INSERT INTO `uploads_collection` (`id`, `entity_id`) VALUES
(5, 0),
(6, 0),
(8, 0),
(12, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `auth_key` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password_reset_token` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `status` smallint(6) NOT NULL DEFAULT '10',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `image_path` int(11) DEFAULT NULL COMMENT 'Изображение|text',
  `user_ip` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'IP|text',
  `image` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Изображение пользователя|image',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `password_reset_token` (`password_reset_token`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=65 ;

--
-- Дамп данных таблицы `user`
--

INSERT INTO `user` (`id`, `username`, `auth_key`, `password_hash`, `password_reset_token`, `email`, `status`, `created_at`, `updated_at`, `image_path`, `user_ip`, `image`) VALUES
(8, 'Vlsirko1', '', '$2y$13$WQiHmFQmQMc7CYq40q2vsenAm697zihLCtkJmEL.KMuoMZ6kTgSKa', NULL, 'Vlsirko@i.ua', 1, 1447419943, 1448892161, 7, NULL, '/upload/models/User/8/HuFcTlj9H-Y-1.jpg'),
(59, 'test@loc.loc', '', '$2y$13$P1koBvHqqn7971O2b4WXfu3jtVQItkaYOu6IeRBYRU2ZrYYptFuJG', NULL, 'test@loc.loc', 1, 1448605896, 1448617134, NULL, NULL, NULL);

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `auth_item`
--
ALTER TABLE `auth_item`
  ADD CONSTRAINT `auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `product_module`
--
ALTER TABLE `product_module`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `product_category_module` (`id`) ON DELETE SET NULL ON UPDATE SET NULL;

--
-- Ограничения внешнего ключа таблицы `uploads`
--
ALTER TABLE `uploads`
  ADD CONSTRAINT `uploads_collection_uploads_FK` FOREIGN KEY (`collection_id`) REFERENCES `uploads_collection` (`id`) ON DELETE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
