-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 192.168.31.39:3306
-- Время создания: Май 07 2023 г., 00:01
-- Версия сервера: 8.0.30
-- Версия PHP: 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
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

CREATE TABLE `carts` (
  `id` int UNSIGNED NOT NULL,
  `userId` int UNSIGNED NOT NULL COMMENT 'id пользователя',
  `productId` int UNSIGNED NOT NULL COMMENT 'id продукта',
  `count` int UNSIGNED NOT NULL DEFAULT '1' COMMENT 'количество ед. товара в корзине'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `carts`
--

INSERT INTO `carts` (`id`, `userId`, `productId`, `count`) VALUES
(1, 3, 1, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'название',
  `href` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ссылка',
  `image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'изображение категории'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`id`, `name`, `href`, `image`) VALUES
(1, 'Аппараты', 'apparaty', '5956de055c6f7839cdeca1fc17f9f2be.png'),
(2, 'Прочее', 'prochee', '9d4c747368d6ca870dfd8086eaa9c6dd.png');

-- --------------------------------------------------------

--
-- Структура таблицы `company`
--

CREATE TABLE `company` (
  `name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'название компании',
  `phone` varchar(12) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'контактный номер телефона',
  `inn` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ИНН',
  `ogrn` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ОГРН',
  `pay_acc` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'номер счёта',
  `bik` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'БИК',
  `ks` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'КС',
  `timezone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'основная временная зона',
  `place` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'адрес'
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

CREATE TABLE `countries` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'название страны'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `countries`
--

INSERT INTO `countries` (`id`, `name`) VALUES
(2, 'Китай'),
(1, 'Россия');

-- --------------------------------------------------------

--
-- Структура таблицы `deliveries`
--

CREATE TABLE `deliveries` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `deliveries`
--

INSERT INTO `deliveries` (`id`, `name`, `link`) VALUES
(1, 'Почта России', 'https://www.pochta.ru/'),
(2, 'СДЭК', 'https://www.cdek.ru/ru/tracking');

-- --------------------------------------------------------

--
-- Структура таблицы `faq`
--

CREATE TABLE `faq` (
  `id` int UNSIGNED NOT NULL,
  `question` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'вопрос',
  `answer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ответ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `faq`
--

INSERT INTO `faq` (`id`, `question`, `answer`) VALUES
(3, 'Вопрос1', 'Ответ1'),
(4, 'Какие гарантии?', 'Какие тебе гарантии нужны?');

-- --------------------------------------------------------

--
-- Структура таблицы `instructions`
--

CREATE TABLE `instructions` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'название инструкции',
  `text` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'текст инструкции',
  `video` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'видео к инструкции'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `orders`
--

CREATE TABLE `orders` (
  `id` int UNSIGNED NOT NULL,
  `userId` int UNSIGNED NOT NULL COMMENT 'id пользователя',
  `fullName` varchar(192) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'имя',
  `status` set('Ожидание оплаты','Обработка платежа','Сборка заказа','Отправлен','Доставлен','Закрыт','Отменён') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Ожидание оплаты' COMMENT 'статус заказа',
  `deliveryId` int UNSIGNED DEFAULT NULL COMMENT 'id доставки',
  `tracking` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'трек-номер отправления',
  `added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'дата заказа',
  `closed` datetime DEFAULT NULL COMMENT 'дата закрытия заказа',
  `phone` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'телефон',
  `address` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'адрес доставки',
  `userComment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'комментарий пользователя к заказу',
  `adminComment` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'комментарий админа',
  `isClosed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'закрыт ли заказ',
  `idWhoClosed` int UNSIGNED DEFAULT NULL COMMENT 'id пользователя, который закрыл заказ',
  `payment_id` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'id оплаты',
  `payment_link` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ссылка на оплату'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orders`
--

INSERT INTO `orders` (`id`, `userId`, `fullName`, `status`, `deliveryId`, `tracking`, `added`, `closed`, `phone`, `address`, `userComment`, `adminComment`, `isClosed`, `idWhoClosed`, `payment_id`, `payment_link`) VALUES
(24, 2, 'Za Lu Pin', 'Ожидание оплаты', NULL, NULL, '2023-03-26 15:55:46', NULL, '+79223127609', '617765, Россия, Пермский край, г. Чайковский, ул. Сосновая, 10, 40', NULL, NULL, 0, NULL, '2533563363', 'https://securepayments.tinkoff.ru/eNc9fItP');

-- --------------------------------------------------------

--
-- Структура таблицы `orders_data`
--

CREATE TABLE `orders_data` (
  `id` int UNSIGNED NOT NULL,
  `orderId` int UNSIGNED NOT NULL COMMENT 'id заказа',
  `productId` int UNSIGNED DEFAULT NULL COMMENT 'id товара',
  `buyPrice` double UNSIGNED NOT NULL COMMENT 'цена, за которую купили товар',
  `count` int UNSIGNED NOT NULL DEFAULT '1' COMMENT 'количество ед. заказанного товара'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `orders_data`
--

INSERT INTO `orders_data` (`id`, `orderId`, `productId`, `buyPrice`, `count`) VALUES
(59, 24, 4, 150, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `pages`
--

CREATE TABLE `pages` (
  `id` int UNSIGNED NOT NULL,
  `fileName` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'название файла',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'название страницы',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'описание страницы',
  `isUniqueDescription` tinyint(1) NOT NULL DEFAULT '0',
  `isMenuPage` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'заносить ли страницу в меню',
  `keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ключевые слова страницы'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(10, 'cart', 'Корзина', 'Корзина пользователя', 0, 0, NULL),
(11, 'order/create', 'Оформление заказа', 'Страница оформления заказа', 0, 0, NULL),
(12, 'terms', 'Политика конфиденциальности и обработки персональных данных', 'Политика конфиденциальности и обработки персональных данных', 0, 0, NULL),
(13, 'recoverPassword', 'Смена пароля', 'Страница смены пароля', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Структура таблицы `payment_objects`
--

CREATE TABLE `payment_objects` (
  `id` int UNSIGNED NOT NULL COMMENT 'id типа',
  `code` varchar(255) NOT NULL COMMENT 'код типа',
  `description` text COMMENT 'описание типа'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Признак предмета расчёта';

--
-- Дамп данных таблицы `payment_objects`
--

INSERT INTO `payment_objects` (`id`, `code`, `description`) VALUES
(1, 'commodity', 'Товар'),
(2, 'excise', 'Подакцизный товар'),
(3, 'job', 'Работа'),
(4, 'service', 'Услуга'),
(5, 'gambling_bet', 'Ставка азартной игры'),
(6, 'gambling_prize', 'Выигрыш азартной игры'),
(7, 'lottery', 'Лотерейный билет'),
(8, 'lottery_prize', 'Выигрыш лотереи'),
(9, 'intellectual_activity', 'Предоставление результатов интеллектуальной деятельности'),
(10, 'payment', 'Платёж'),
(11, 'agent_commission', 'Агентское вознаграждение'),
(12, 'composite', 'Составной предмет расчета'),
(13, 'another', 'Иной предмет расчета');

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `products` (
  `id` int UNSIGNED NOT NULL,
  `categoryId` int UNSIGNED NOT NULL COMMENT 'id категории',
  `countryId` int UNSIGNED NOT NULL COMMENT 'id страны',
  `instructionId` int UNSIGNED DEFAULT NULL COMMENT 'id инструкции',
  `payment_object_id` int UNSIGNED NOT NULL DEFAULT '1' COMMENT 'id признака предмета расчёта',
  `href` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ссылка на товар',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'название',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'описание',
  `height` double DEFAULT NULL COMMENT 'параметр: высота',
  `width` double DEFAULT NULL COMMENT 'параметр: ширина',
  `length` double DEFAULT NULL COMMENT 'параметр: длина',
  `features` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'преймещества',
  `techSpec` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'характеристики',
  `count` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'количество',
  `price` double UNSIGNED NOT NULL COMMENT 'цена',
  `sale` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'скидка на това, %',
  `preOrder` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'возможен ли предзаказ',
  `keywords` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'ключевые слова',
  `sold` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'сколько продано',
  `added` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'дата добавления',
  `isDeleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'удалён ли товар'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `categoryId`, `countryId`, `instructionId`, `payment_object_id`, `href`, `name`, `description`, `height`, `width`, `length`, `features`, `techSpec`, `count`, `price`, `sale`, `preOrder`, `keywords`, `sold`, `added`, `isDeleted`) VALUES
(1, 1, 2, NULL, 1, 'apparat-krutoy', 'Аппарат крутой', 'ава', NULL, NULL, NULL, '1111111111111;111111111111435;5235', NULL, 39, 25001.03, 5, 0, NULL, 9, '2023-02-16 14:03:19', 0),
(3, 2, 2, NULL, 1, 'gel-dlya-popy', 'Гель для попы', 'оч хороший', NULL, NULL, NULL, 'Увлажняет;Быстро впитывается', 'Хуйня:полная;Залупа:конская', 4, 599.99, 0, 0, NULL, 29, '2023-02-16 14:03:19', 0),
(4, 2, 2, NULL, 1, 'gigienicheskaya-pomada', 'Гигиеническая помада', 'ава', NULL, NULL, NULL, NULL, NULL, 141, 150, 0, 0, NULL, 32, '2023-02-16 14:03:19', 0),
(5, 1, 2, NULL, 1, 'apparat-krutoy3', 'Аппарат крутой3', 'ава', NULL, NULL, NULL, '1111111111111;111111111111435;5235', NULL, 4, 25001.03, 5, 0, NULL, 1, '2023-02-16 14:03:19', 0),
(7, 1, 2, NULL, 1, 'apparat-krutoy5', 'Аппарат крутой5', 'ава', NULL, NULL, NULL, '1111111111111;111111111111435;5235', NULL, 0, 25001.03, 5, 0, NULL, 0, '2023-02-16 14:03:19', 0),
(8, 1, 2, NULL, 1, 'apparat-krutoy6', 'Аппарат крутой6', 'ава', NULL, NULL, NULL, '1111111111111;111111111111435;5235', NULL, 5, 25001.03, 5, 0, NULL, 0, '2023-02-16 14:03:19', 0),
(9, 1, 2, NULL, 1, 'apparat-krutoy7', 'Аппарат крутой7', 'ава', NULL, NULL, NULL, '1111111111111;111111111111435;5235', NULL, 5, 25001.03, 5, 0, NULL, 0, '2023-02-16 14:03:19', 0);

-- --------------------------------------------------------

--
-- Структура таблицы `products_images`
--

CREATE TABLE `products_images` (
  `id` int UNSIGNED NOT NULL,
  `productId` int UNSIGNED NOT NULL COMMENT 'id товара',
  `image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'имя файла картинки',
  `isMain` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'является ли изображение основным'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `products_images`
--

INSERT INTO `products_images` (`id`, `productId`, `image`, `isMain`) VALUES
(1, 1, '172bda5e349a98fc124084017f36261c.jpg', 1),
(2, 1, 'e3b6af6bd4df2ff56508097d5ddb47c1.jpg', 0),
(5, 1, 'AJf99u2h3o.jpg', 0),
(6, 1, 'c1c9af077c8eb785f31464755b68d61b.jpg', 0),
(8, 3, '15f2678f14f6f74f5638e589fca2f66e.jpg', 1),
(9, 4, '54f874f67be70507596e26cd5b875793.png', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `promocodes`
--

CREATE TABLE `promocodes` (
  `id` int UNSIGNED NOT NULL,
  `code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'промокод',
  `sale` double NOT NULL DEFAULT '0' COMMENT 'скидка, %',
  `startDate` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'действует с этой даты',
  `endDate` datetime DEFAULT NULL COMMENT 'действует до этой даты'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `reserve_products`
--

CREATE TABLE `reserve_products` (
  `id` int UNSIGNED NOT NULL COMMENT 'ID',
  `userId` int UNSIGNED NOT NULL COMMENT 'ID пользователя',
  `productId` int UNSIGNED NOT NULL COMMENT 'ID товара',
  `count` int UNSIGNED NOT NULL COMMENT 'количество зарезервированного товара',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'дата резервирования'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Сюда попадают товары, которые участвую в процессе оформления';

-- --------------------------------------------------------

--
-- Структура таблицы `smtp_settings`
--

CREATE TABLE `smtp_settings` (
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `host` varchar(255) NOT NULL,
  `cipher` varchar(100) NOT NULL,
  `port` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `smtp_settings`
--

INSERT INTO `smtp_settings` (`email`, `password`, `host`, `cipher`, `port`) VALUES
('no-reply@movikk.ru', 'wa57x3Zm', 'smtp.timeweb.ru', 'ssl', 465);

-- --------------------------------------------------------

--
-- Структура таблицы `socials`
--

CREATE TABLE `socials` (
  `id` int UNSIGNED NOT NULL,
  `title` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'отображаемый текст',
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'название',
  `href` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ссылка',
  `shortKey` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'ключ для тегов',
  `isPhoneNumber` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'является ли номером телефона'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- Структура таблицы `tinkoff_settings`
--

CREATE TABLE `tinkoff_settings` (
  `TerminalKey` varchar(255) NOT NULL,
  `TerminalPassword` varchar(255) NOT NULL,
  `Taxation` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `SuccessURL` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `FailURL` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `tinkoff_settings`
--

INSERT INTO `tinkoff_settings` (`TerminalKey`, `TerminalPassword`, `Taxation`, `SuccessURL`, `FailURL`) VALUES
('1676738701979DEMO', '5cw2mq0tj4bk0xtn', 'patent', 'https://movikk.ru/set_order_success.html', 'https://movikk.ru/set_order_error.html');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'электронная почта',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'пароль',
  `name` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'имя',
  `surname` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'фамилия',
  `patronymic` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'отчество',
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'является ли пользователь администратором',
  `hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'хеш при регистрации',
  `isEmailConfirmed` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'подтверждён ли email',
  `ban` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'заблокирован ли пользователь',
  `recoveryHash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recoveryHashDate` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `email`, `password`, `name`, `surname`, `patronymic`, `isAdmin`, `hash`, `isEmailConfirmed`, `ban`, `recoveryHash`, `recoveryHashDate`) VALUES
(2, 'chairowner@yandex.ru', '$2y$10$cEH/MzDTg7fOHOksVgf9IuD7SLuyIrsYVhnHojjCMseHq6deMwi5K', 'Данил', 'Сагайдачный', NULL, 1, '9b8c32b7447586c7cf245d32aa900e87', 1, 0, NULL, NULL),
(3, 'tony-crash@mail.ru', '$2y$10$fhpKTBgPqBTD8AU3n5gtmufBb1fl6Epj3yuafP0v8iUZl5Y0Xq6I2', 'Данил', '2', 'Александрович', 0, '1c32b7447586c7c3546349b8f245d32aa900e87', 1, 0, NULL, NULL),
(5, 'sagajdachnyj2002@11mail.ru', '$2y$10$fhpKTBgPqBTD8AU3n5gtmufBb1fl6Epj3yuafP0v8iUZl5Y0Xq6I2', 'Анастасия', '3', 'Олеговна', 0, '6340e8719b8f245d32aa90c32b7447586c7c354', 1, 0, NULL, NULL),
(8, 'sagajdachnyj21002@mail.ru', '$2y$10$fhpKTBgPqBTD8AU3n5gtmufBb1fl6Epj3yuafP0v8iUZl5Y0Xq6I2', 'Анастасия', '4', 'Олеговна', 0, '6340e8719b8f245d32aa90c32b7447586c7c3541', 1, 0, NULL, NULL),
(9, 'sagajdachnyj21002@mail.ru1', '$2y$10$fhpKTBgPqBTD8AU3n5gtmufBb1fl6Epj3yuafP0v8iUZl5Y0Xq6I2', 'Анастасия', '5', 'Олеговна', 0, '6340e8719b8f245d32aa90c32b7447586c7c35411', 1, 0, NULL, NULL),
(20, 'chairowner@yandex.ru12', '$2y$10$fhpKTBgPqBTD8AU3n5gtmufBb1fl6Epj3yuafP0v8iUZl5Y0Xq6I2', 'Данил', '6', NULL, 1, '9b8c32b7447586c7cf245d32aa900e8712', 1, 0, NULL, NULL),
(21, 'tony-crash@mail.ru12', '$2y$10$fhpKTBgPqBTD8AU3n5gtmufBb1fl6Epj3yuafP0v8iUZl5Y0Xq6I2', 'Данил', '7', 'Александрович', 0, '1c32b7447586c7c3546349b8f245d32aa900e8712', 1, 0, NULL, NULL),
(22, 'sagajdachnyj2002@mail.ru12', '$2y$10$fhpKTBgPqBTD8AU3n5gtmufBb1fl6Epj3yuafP0v8iUZl5Y0Xq6I2', 'Анастасия', '8', 'Олеговна', 0, '6340e8719b8f245d32aa90c32b7447586c7c35412', 1, 0, NULL, NULL),
(23, 'sagajdachnyj21002@mail.ru12', '$2y$10$fhpKTBgPqBTD8AU3n5gtmufBb1fl6Epj3yuafP0v8iUZl5Y0Xq6I2', 'Анастасия', '9', 'Олеговна', 0, '6340e8719b8f245d32aa90c32b7447586c7c3541122', 1, 0, NULL, NULL),
(24, 'sagajdachnyj21002@mail.ru122', '$2y$10$fhpKTBgPqBTD8AU3n5gtmufBb1fl6Epj3yuafP0v8iUZl5Y0Xq6I2', 'Анастасия', '10', 'Олеговна', 0, '6340e8719b8f245d32aa90c32b7447586c75c3541122', 1, 0, NULL, NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `productId` (`productId`),
  ADD KEY `userId` (`userId`);

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `deliveries`
--
ALTER TABLE `deliveries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD UNIQUE KEY `link` (`link`);

--
-- Индексы таблицы `faq`
--
ALTER TABLE `faq`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `question` (`question`);

--
-- Индексы таблицы `instructions`
--
ALTER TABLE `instructions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `payment_id` (`payment_id`),
  ADD KEY `userId` (`userId`),
  ADD KEY `idWhoClosed` (`idWhoClosed`),
  ADD KEY `deliveryId` (`deliveryId`);

--
-- Индексы таблицы `orders_data`
--
ALTER TABLE `orders_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `productId` (`productId`),
  ADD KEY `orderId` (`orderId`);

--
-- Индексы таблицы `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fileName` (`fileName`);

--
-- Индексы таблицы `payment_objects`
--
ALTER TABLE `payment_objects`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoryId` (`categoryId`),
  ADD KEY `countryId` (`countryId`),
  ADD KEY `instructionId` (`instructionId`),
  ADD KEY `payment_object_id` (`payment_object_id`);

--
-- Индексы таблицы `products_images`
--
ALTER TABLE `products_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `productId` (`productId`);

--
-- Индексы таблицы `promocodes`
--
ALTER TABLE `promocodes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Индексы таблицы `reserve_products`
--
ALTER TABLE `reserve_products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `userId` (`userId`),
  ADD KEY `productId` (`productId`);

--
-- Индексы таблицы `socials`
--
ALTER TABLE `socials`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shortKey` (`shortKey`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `hash` (`hash`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `carts`
--
ALTER TABLE `carts`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT для таблицы `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `countries`
--
ALTER TABLE `countries`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `deliveries`
--
ALTER TABLE `deliveries`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `faq`
--
ALTER TABLE `faq`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `instructions`
--
ALTER TABLE `instructions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT для таблицы `orders_data`
--
ALTER TABLE `orders_data`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT для таблицы `pages`
--
ALTER TABLE `pages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `payment_objects`
--
ALTER TABLE `payment_objects`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id типа', AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT для таблицы `products`
--
ALTER TABLE `products`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `products_images`
--
ALTER TABLE `products_images`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `promocodes`
--
ALTER TABLE `promocodes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `reserve_products`
--
ALTER TABLE `reserve_products`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `socials`
--
ALTER TABLE `socials`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

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
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`deliveryId`) REFERENCES `deliveries` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_ibfk_4` FOREIGN KEY (`idWhoClosed`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `orders_data`
--
ALTER TABLE `orders_data`
  ADD CONSTRAINT `orders_data_ibfk_1` FOREIGN KEY (`orderId`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_data_ibfk_2` FOREIGN KEY (`productId`) REFERENCES `products` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_3` FOREIGN KEY (`instructionId`) REFERENCES `instructions` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `products_ibfk_4` FOREIGN KEY (`categoryId`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `products_ibfk_5` FOREIGN KEY (`countryId`) REFERENCES `countries` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `products_ibfk_6` FOREIGN KEY (`payment_object_id`) REFERENCES `payment_objects` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `products_images`
--
ALTER TABLE `products_images`
  ADD CONSTRAINT `products_images_ibfk_1` FOREIGN KEY (`productId`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ограничения внешнего ключа таблицы `reserve_products`
--
ALTER TABLE `reserve_products`
  ADD CONSTRAINT `reserve_products_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `reserve_products_ibfk_2` FOREIGN KEY (`productId`) REFERENCES `products` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
