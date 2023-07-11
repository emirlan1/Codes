-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Июн 12 2023 г., 13:52
-- Версия сервера: 8.0.19
-- Версия PHP: 7.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `test`
--

-- --------------------------------------------------------

--
-- Структура таблицы `plots`
--

CREATE TABLE `plots` (
  `plot_id` bigint UNSIGNED NOT NULL,
  `status` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `billing` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `number` varchar(10) NOT NULL DEFAULT '',
  `size` smallint UNSIGNED NOT NULL DEFAULT '0',
  `price` int UNSIGNED NOT NULL DEFAULT '0',
  `base_fixed` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `electricity_t1` float(11,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `electricity_t2` float(11,2) UNSIGNED NOT NULL DEFAULT '0.00',
  `updated` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `plots`
--

INSERT INTO `plots` (`plot_id`, `status`, `billing`, `number`, `size`, `price`, `base_fixed`, `electricity_t1`, `electricity_t2`, `updated`) VALUES
(1, 2, 1, '1', 13, 2064378, 0, 0.00, 0.00, 1682233937),
(2, 2, 1, '2', 10, 3367020, 0, 1813.27, 822.59, 1680507313),
(3, 2, 1, '3', 8, 3300000, 0, 0.00, 0.00, 1680514598),
(4, 2, 0, '4', 9, 3025626, 0, 0.00, 0.00, 1677858220),
(5, 2, 0, '5', 21, 6795138, 0, 100.00, 0.00, 1668015240),
(6, 2, 1, '7', 88, 25215000, 1, 0.00, 0.00, 1680514641),
(7, 2, 1, '8', 40, 12500000, 1, 0.00, 0.00, 1680514650),
(8, 2, 1, '9', 43, 13300000, 1, 18637.77, 8762.21, 1680507541),
(9, 0, 1, '10', 41, 15500000, 1, 0.00, 0.00, 1679915811),
(10, 2, 1, '11', 35, 8672101, 1, 0.00, 0.00, 1680514657),
(11, 0, 0, '12', 28, 9900000, 1, 0.00, 0.00, 1680517298),
(12, 2, 1, '18', 18, 4152100, 0, 0.00, 0.00, 1680514669),
(13, 2, 1, '19', 17, 4408000, 0, 12966.18, 5275.78, 1680508300),
(14, 2, 1, '20', 17, 4408000, 0, 0.00, 0.00, 1680514678),
(15, 2, 1, '21', 20, 5200900, 0, 1944.13, 1230.12, 1680508369),
(16, 2, 1, '22', 20, 5200900, 0, 0.00, 0.00, 1680514686),
(17, 2, 1, '23', 17, 4046000, 0, 0.00, 0.00, 1680514704),
(18, 2, 1, '24', 16, 3808000, 0, 0.00, 0.00, 1680514788),
(19, 2, 1, '25', 16, 3808000, 0, 0.00, 0.00, 1680514780),
(20, 2, 1, '26', 15, 3570000, 0, 0.00, 0.00, 1680514774),
(21, 2, 1, '27', 16, 38080001, 0, 0.00, 0.00, 1686407151),
(22, 2, 1, '29', 22, 4473000, 0, 0.00, 0.00, 1680514764),
(23, 2, 1, '30', 12, 2700000, 0, 0.00, 0.00, 1680514759),
(24, 2, 1, '31', 12, 2610000, 0, 515.49, 339.39, 1680508971),
(25, 2, 1, '32', 12, 2610000, 0, 0.00, 0.00, 1680514726),
(26, 2, 1, '33', 10, 2470900, 0, 0.00, 0.00, 1680507739),
(27, 2, 1, '34', 12, 2610000, 0, 6883.92, 3795.69, 1680508516),
(28, 2, 1, '35', 14, 2765700, 0, 0.00, 0.00, 1680514752),
(29, 2, 1, '36', 11, 2390800, 0, 0.00, 0.00, 1680514746),
(30, 0, 0, '37', 45, 99724001, 0, 0.00, 0.00, 1686407164),
(339, 1, 1, '900', 1, 1, 0, 0.00, 0.00, 1686407716);

-- --------------------------------------------------------

--
-- Структура таблицы `plot_user`
--

CREATE TABLE `plot_user` (
  `id` int NOT NULL,
  `plot_id` int NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `plot_user`
--

INSERT INTO `plot_user` (`id`, `plot_id`, `user_id`) VALUES
(28, 1, 43),
(29, 2, 43),
(30, 22, 43),
(37, 11, 35),
(38, 15, 35),
(39, 8, 35),
(40, 11, 45),
(41, 15, 45),
(42, 8, 45),
(46, 1, 47),
(47, 2, 47);

-- --------------------------------------------------------

--
-- Структура таблицы `sessions`
--

CREATE TABLE `sessions` (
  `sid` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL DEFAULT '0',
  `token` char(40) NOT NULL DEFAULT '',
  `access` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `tz` smallint NOT NULL DEFAULT '0',
  `created` int UNSIGNED NOT NULL DEFAULT '0',
  `logged` int UNSIGNED NOT NULL DEFAULT '0',
  `updated` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `sessions`
--

INSERT INTO `sessions` (`sid`, `user_id`, `token`, `access`, `tz`, `created`, `logged`, `updated`) VALUES
(1, 1, 'ab8dc1e93d4b8fc87647c34e792b95e0902b491c', 1, 240, 1686239445, 1686239445, 1686239882),
(2, 1, '67edc962a32bca99a208e442ef7cbd95ce214a59', 1, 240, 1686337517, 1686337517, 1686567139);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `user_id` bigint UNSIGNED NOT NULL,
  `village_id` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `plot_id` varchar(255) NOT NULL DEFAULT '',
  `access` tinyint(1) NOT NULL DEFAULT '0',
  `first_name` varchar(255) NOT NULL DEFAULT '',
  `last_name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `phone` bigint UNSIGNED NOT NULL DEFAULT '0',
  `phone_code` varchar(4) NOT NULL DEFAULT '',
  `phone_attempts_code` int UNSIGNED NOT NULL DEFAULT '0',
  `phone_attempts_sms` int UNSIGNED NOT NULL DEFAULT '0',
  `updated` int UNSIGNED NOT NULL DEFAULT '0',
  `last_login` int UNSIGNED NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`user_id`, `village_id`, `plot_id`, `access`, `first_name`, `last_name`, `email`, `phone`, `phone_code`, `phone_attempts_code`, `phone_attempts_sms`, `updated`, `last_login`) VALUES
(1, 1, '1', 1, 'Aweq', 'Ewq', 'testgrowave@gmail.com', 54555541, '1111', 0, 9, 0, 1686337517),
(2, 1, '2', 1, 'Martin', 'Kurian', 'test2@gmail.com', 971000000002, '1111', 0, 0, 0, 1686227116),
(3, 1, '3', 1, 'Nisha', 'Banchi', 'test3@gmail.com', 971000000003, '1111', 0, 0, 0, 1686227116),
(4, 1, '4', 1, 'Umang', 'Nayak', 'test4@gmail.com', 971000000004, '1111', 0, 0, 0, 1686227116),
(5, 1, '5', 1, 'Mahira', 'Hussain', 'test5@gmail.com', 971000000005, '1111', 0, 0, 0, 1686227116),
(6, 1, '6', 1, 'Muhammad ', 'Ali', 'test6@gmail.com', 971000000006, '1111', 0, 0, 0, 1686227116),
(7, 1, '7', 1, 'Yanina', 'Payares', 'test7@gmail.com', 971000000007, '1111', 0, 0, 0, 1686227116),
(8, 1, '8', 1, 'Iffat', 'Shahzad', 'test8@gmail.com', 971000000008, '1111', 0, 0, 0, 1686227116),
(9, 1, '9', 1, 'Izem', 'Yilmaz', 'test9@gmail.com', 971000000009, '1111', 0, 0, 0, 1686227116),
(10, 1, '5', 1, 'Mhiz', 'Brainy', 'test10@gmail.com', 971000000010, '1111', 0, 0, 0, 1686227116),
(11, 1, '7', 1, 'Nasir', 'Mughal', 'test11@gmail.com', 971000000011, '1111', 0, 0, 0, 1686227116),
(12, 1, '12', 1, 'Jorgen', 'Jorgensen', 'test12@gmail.com', 971000000012, '1111', 0, 0, 0, 1686227116),
(13, 1, '13', 1, 'Lennis', 'Nabalayo', 'test13@gmail.com', 971000000013, '1111', 0, 0, 0, 1686227116),
(14, 1, '5', 1, 'Vipul', 'Bansode', 'test14@gmail.com', 971000000014, '1111', 0, 0, 0, 1686227116),
(15, 1, '15', 1, 'Marina', 'Fonf', 'test15@gmail.com', 971000000015, '1111', 0, 0, 0, 1686227116),
(16, 1, '16', 1, 'Shamir', 'Khan', 'test16@gmail.com', 971000000016, '1111', 0, 0, 0, 1686227116),
(17, 1, '17', 1, 'Ricardo', 'Sabularse', 'test17@gmail.com', 971000000017, '1111', 0, 0, 0, 1686227116),
(18, 1, '18', 1, 'Roger', 'Alam', 'test18@gmail.com', 971000000018, '1111', 0, 0, 0, 1686227116),
(19, 1, '19', 1, 'Vam', 'Kannambra', 'test19@gmail.com', 971000000019, '1111', 0, 0, 0, 1686227116),
(20, 1, '20', 1, 'Naiel', 'Zemour', 'test20@gmail.com', 971000000020, '1111', 0, 0, 0, 1686227116),
(21, 1, '21', 1, 'Sajad2', 'Bobs', 'test21@gmail.com', 971000000021, '1111', 0, 0, 0, 1686227116),
(22, 1, '20', 1, 'Mumtaz', 'Falak', 'test22@gmail.com', 971000000022, '1111', 0, 0, 0, 1686227116),
(35, 0, '', 0, 'TestName', 'TestName2', 'TestEmail@mail.com', 8123912391, '', 0, 0, 0, 0),
(43, 0, '', 0, '1231', '123', '123', 123, '', 0, 0, 0, 0),
(45, 0, '', 0, 'Jonh', 'Bob', 'test55@gmail.com', 218841000123, '', 0, 0, 0, 0),
(47, 0, '', 0, '123', '123', 'rwewewedsda@gmail.com', 123, '', 0, 0, 0, 0);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `plots`
--
ALTER TABLE `plots`
  ADD PRIMARY KEY (`plot_id`),
  ADD KEY `number` (`number`),
  ADD KEY `billing` (`billing`) USING BTREE;

--
-- Индексы таблицы `plot_user`
--
ALTER TABLE `plot_user`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plot_user_fk_key` (`user_id`);

--
-- Индексы таблицы `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`sid`),
  ADD KEY `token` (`token`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD KEY `phone` (`phone`),
  ADD KEY `plot_id` (`plot_id`(191)) USING BTREE;

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `plots`
--
ALTER TABLE `plots`
  MODIFY `plot_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=340;

--
-- AUTO_INCREMENT для таблицы `plot_user`
--
ALTER TABLE `plot_user`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT для таблицы `sessions`
--
ALTER TABLE `sessions`
  MODIFY `sid` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `user_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `plot_user`
--
ALTER TABLE `plot_user`
  ADD CONSTRAINT `plot_user_fk_key` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
