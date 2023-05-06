-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Фев 07 2023 г., 23:35
-- Версия сервера: 5.7.35-38
-- Версия PHP: 7.4.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `cv33474_movikk`
--

-- --------------------------------------------------------

--
-- Структура таблицы `carts`
--

CREATE TABLE IF NOT EXISTS `carts` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` int(10) UNSIGNED NOT NULL COMMENT 'id пользователя',
  `productId` int(10) UNSIGNED NOT NULL COMMENT 'id продукта',
  `count` int(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'количество ед. товара в корзине',
  PRIMARY KEY (`id`),
  KEY `productId` (`productId`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'название',
  `href` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ссылка',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `company`
--

CREATE TABLE IF NOT EXISTS `company` (
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'название компании',
  `phone` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'контактный номер телефона',
  `inn` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ИНН',
  `ogrn` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ОГРН',
  `pay_acc` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'номер счёта',
  `bik` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'БИК',
  `ks` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'КС',
  `timezone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'основная временная зона',
  `place` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'адрес'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `company`
--

INSERT INTO `company` (`name`, `phone`, `inn`, `ogrn`, `pay_acc`, `bik`, `ks`, `timezone`, `place`) VALUES
('MoVikk', '+79223127607', '592011319403', '314595810600339', '40802810900000116741', '044525974', '30101810145250000974', 'Asia/Yekaterinburg', NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `countries`
--

CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'название страны',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `deliveries`
--

CREATE TABLE IF NOT EXISTS `deliveries` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `link` (`link`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `faq`
--

CREATE TABLE IF NOT EXISTS `faq` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `question` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'вопрос',
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ответ',
  PRIMARY KEY (`id`),
  UNIQUE KEY `question` (`question`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `instructions`
--

CREATE TABLE IF NOT EXISTS `instructions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'название инструкции',
  `text` text COLLATE utf8mb4_unicode_ci COMMENT 'текст инструкции',
  `video` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'видео к инструкции',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE IF NOT EXISTS `orders` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` int(10) UNSIGNED NOT NULL COMMENT 'id пользователя',
  `status` set('Ожидание оплаты','Обработка платежа','Сборка заказа','Отправлен','Доставлен','Закрыт','Отменён') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Ожидание оплаты' COMMENT 'статус заказа',
  `deliveryId` int(10) UNSIGNED DEFAULT NULL COMMENT 'id доставки',
  `tracking` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'трек-номер отправления',
  `added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'дата заказа',
  `closed` datetime DEFAULT NULL COMMENT 'дата закрытия заказа',
  `address` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'адрес доставки',
  `comment` text COLLATE utf8mb4_unicode_ci COMMENT 'комментарий к заказу',
  `isClosed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'закрыт ли заказ',
  `idWhoClosed` int(10) UNSIGNED DEFAULT NULL COMMENT 'id пользователя, который закрыл заказ',
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `idWhoClosed` (`idWhoClosed`),
  KEY `deliveryId` (`deliveryId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orders_data`
--

CREATE TABLE IF NOT EXISTS `orders_data` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `orderId` int(10) UNSIGNED NOT NULL COMMENT 'id заказа',
  `productId` int(10) UNSIGNED DEFAULT NULL COMMENT 'id товара',
  `buyPrice` double UNSIGNED NOT NULL COMMENT 'цена, за которую купили товар',
  `count` int(10) UNSIGNED NOT NULL DEFAULT '1' COMMENT 'количество ед. заказанного товара',
  PRIMARY KEY (`id`),
  KEY `productId` (`productId`),
  KEY `orderId` (`orderId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fileName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'название файла',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'название страницы',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'описание страницы',
  `isUniqueDescription` tinyint(1) NOT NULL DEFAULT '0',
  `isMenuPage` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'заносить ли страницу в меню',
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ключевые слова страницы',
  PRIMARY KEY (`id`),
  UNIQUE KEY `fileName` (`fileName`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `pages`
--

INSERT INTO `pages` (`id`, `fileName`, `title`, `description`, `isUniqueDescription`, `isMenuPage`, `keywords`) VALUES
(1, 'index', 'MoVikk', 'Оборудование для салонов красоты и расходники по приятным ценам', 1, 1, NULL),
(2, 'shop', 'Каталог', NULL, 0, 1, NULL),
(3, 'study', 'Обучение', NULL, 0, 1, NULL),
(4, 'faq', 'FAQ', NULL, 0, 1, NULL),
(5, 'product', 'Страница товара', NULL, 1, 0, NULL),
(6, '404', 'Страница не найдена', NULL, 0, 0, NULL),
(7, '503', 'Ведутся технические работы', NULL, 0, 0, NULL),
(8, 'lk', 'Личный кабинет', 'Личный кабинет пользователя', 1, 0, NULL),
(9, 'orders', 'Заказы', 'Страница заказов', 0, 0, NULL),
(10, 'cart', 'Корзина', 'Корзина пользователя', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `categoryId` int(10) UNSIGNED NOT NULL COMMENT 'id категории',
  `countryId` int(10) UNSIGNED NOT NULL COMMENT 'id страны',
  `instructionId` int(10) UNSIGNED DEFAULT NULL COMMENT 'id инструкции',
  `href` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ссылка на товар',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'название',
  `description` text COLLATE utf8mb4_unicode_ci COMMENT 'описание',
  `height` double DEFAULT NULL COMMENT 'параметр: высота',
  `width` double DEFAULT NULL COMMENT 'параметр: ширина',
  `length` double DEFAULT NULL COMMENT 'параметр: длина',
  `features` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'преймещества',
  `techSpec` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'характеристики',
  `count` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'количество',
  `price` double UNSIGNED NOT NULL COMMENT 'цена',
  `sale` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'скидка на това, %',
  `preOrder` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'возможен ли предзаказ',
  `keywords` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ключевые слова',
  `sold` int(10) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'сколько продано',
  `added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'дата добавления',
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'удалён ли товар',
  PRIMARY KEY (`id`),
  KEY `categoryId` (`categoryId`),
  KEY `countryId` (`countryId`),
  KEY `instructionId` (`instructionId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `products_images`
--

CREATE TABLE IF NOT EXISTS `products_images` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `productId` int(10) UNSIGNED NOT NULL COMMENT 'id товара',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'имя файла картинки',
  `isMain` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'является ли изображение основным',
  PRIMARY KEY (`id`),
  KEY `productId` (`productId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `promocodes`
--

CREATE TABLE IF NOT EXISTS `promocodes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'промокод',
  `sale` double NOT NULL DEFAULT '0' COMMENT 'скидка, %',
  `startDate` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'действует с этой даты',
  `endDate` datetime DEFAULT NULL COMMENT 'действует до этой даты',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `socials`
--

CREATE TABLE IF NOT EXISTS `socials` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'отображаемый текст',
  `name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'название',
  `href` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ссылка',
  `shortKey` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ключ для тегов',
  `isPhoneNumber` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'является ли номером телефона',
  PRIMARY KEY (`id`),
  UNIQUE KEY `shortKey` (`shortKey`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `socials`
--

INSERT INTO `socials` (`id`, `title`, `name`, `href`, `shortKey`, `isPhoneNumber`) VALUES
(1, 'vk.com/movikk', 'ВКонтакте', 'https://vk.com/movikk', 'vk', 0),
(2, '@movikk', 'Инстаграм', 'https://instagram.com/movikk', 'instagram', 0),
(7, 'WhatsApp', 'WhatsApp', '+79223127607', 'whatsapp', 1),
(8, 'Viber', 'Viber', '+79223127607', 'viber', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'электронная почта',
  `phone` varchar(13) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'номер телефона',
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'пароль',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'имя',
  `surname` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'фамилия',
  `patronymic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'отчество',
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'является ли пользователь администратором',
  `hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'хеш при регистрации',
  `isEmailConfirmed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'подтверждён ли email',
  `ban` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'заблокирован ли пользователь',
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  UNIQUE KEY `hash` (`hash`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `users_addresses`
--

CREATE TABLE IF NOT EXISTS `users_addresses` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `userId` int(10) UNSIGNED NOT NULL COMMENT 'id пользователя',
  `country` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'страна',
  `region` varchar(170) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'регион',
  `locality` varchar(170) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'населённый пункт',
  `street` varchar(170) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'улица',
  `building` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'строение',
  `postcode` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`productId`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`deliveryId`) REFERENCES `deliveries` (`id`) ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders_data`
--
ALTER TABLE `orders_data`
  ADD CONSTRAINT `orders_data_ibfk_1` FOREIGN KEY (`orderId`) REFERENCES `orders` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_data_ibfk_2` FOREIGN KEY (`productId`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`instructionId`) REFERENCES `instructions` (`id`) ON UPDATE SET NULL,
  ADD CONSTRAINT `products_ibfk_4` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `products_ibfk_5` FOREIGN KEY (`countryId`) REFERENCES `countries` (`id`);

--
-- Ограничения внешнего ключа таблицы `products_images`
--
ALTER TABLE `products_images`
  ADD CONSTRAINT `products_images_ibfk_1` FOREIGN KEY (`productId`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `users_addresses`
--
ALTER TABLE `users_addresses`
  ADD CONSTRAINT `users_addresses_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
