-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Ноя 29 2018 г., 11:04
-- Версия сервера: 5.7.20
-- Версия PHP: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `elib`
--

-- --------------------------------------------------------

--
-- Структура таблицы `authors`
--

CREATE TABLE `authors` (
  `ar_id` int(11) NOT NULL,
  `ar_first_name` varchar(255) DEFAULT NULL,
  `ar_last_name` varchar(255) NOT NULL,
  `ar_middle_name` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `books`
--

CREATE TABLE `books` (
  `bk_id` int(11) NOT NULL,
  `bk_ur_id` int(11) NOT NULL,
  `bk_book_id` varchar(255) NOT NULL,
  `bk_title` varchar(255) NOT NULL,
  `bk_annotation` text NOT NULL,
  `bk_file_date` date NOT NULL,
  `bk_file` varchar(255) NOT NULL,
  `bk_cover` mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `books_authors`
--

CREATE TABLE `books_authors` (
  `bkar_id` int(11) NOT NULL,
  `bkar_bk_id` int(11) NOT NULL,
  `bkar_ar_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `books_genres`
--

CREATE TABLE `books_genres` (
  `bkge_id` int(11) NOT NULL,
  `bkge_bk_id` int(11) NOT NULL,
  `bkge_ge_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `books_sequence`
--

CREATE TABLE `books_sequence` (
  `bkse_id` int(11) NOT NULL,
  `bkse_bk_id` int(11) NOT NULL,
  `bkse_se_id` int(11) NOT NULL,
  `bkse_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `genres`
--

CREATE TABLE `genres` (
  `ge_id` int(11) NOT NULL,
  `ge_gg_id` int(11) NOT NULL,
  `ge_code` varchar(32) NOT NULL,
  `ge_title` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `genres`
--

INSERT INTO `genres` (`ge_id`, `ge_gg_id`, `ge_code`, `ge_title`) VALUES
(1, 1, 'sf_history', 'Альтернативная история'),
(2, 1, 'sf_action', 'Боевая фантастика'),
(3, 1, 'sf_epic', 'Эпическая фантастика'),
(4, 1, 'sf_heroic', 'Героическая фантастика'),
(5, 1, 'sf_detective', 'Детективная фантастика'),
(6, 1, 'sf_cyberpunk', 'Киберпанк'),
(7, 1, 'sf_space', 'Космическая фантастика'),
(8, 1, 'sf_social', 'Социально-психологическая фантастика'),
(9, 1, 'sf_horror', 'Ужасы и Мистика '),
(10, 1, 'sf_humor', 'Юмористическая фантастика '),
(11, 1, 'sf_fantasy', 'Фэнтези'),
(12, 1, 'sf', 'Научная Фантастика');

-- --------------------------------------------------------

--
-- Структура таблицы `genres_groups`
--

CREATE TABLE `genres_groups` (
  `gg_id` int(11) NOT NULL,
  `gg_title` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `genres_groups`
--

INSERT INTO `genres_groups` (`gg_id`, `gg_title`) VALUES
(2, 'Детективы и Триллеры'),
(6, 'Детское'),
(12, 'Документальная литература'),
(15, 'Домоводство (Дом и семья)'),
(10, 'Компьютеры и Интернет'),
(4, 'Любовные романы'),
(9, 'Наука, Образование'),
(7, 'Поэзия, Драматургия'),
(5, 'Приключения'),
(3, 'Проза'),
(13, 'Религия и духовность'),
(11, 'Справочная литература'),
(8, 'Старинное'),
(1, 'Фантастика (Научная фантастика и Фэнтези)'),
(14, 'Юмор');

-- --------------------------------------------------------

--
-- Структура таблицы `sequence`
--

CREATE TABLE `sequence` (
  `se_id` int(11) NOT NULL,
  `se_title` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `ur_id` int(11) NOT NULL,
  `ur_login` varchar(50) NOT NULL,
  `ur_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`ur_id`, `ur_login`, `ur_hash`) VALUES
(1, 'qwerty', '$2y$10$aUvnivc6AVIoF4wMRd06AOk45/NN0RX0sIUxKaaUK56LhHmrPV1lu');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `authors`
--
ALTER TABLE `authors`
  ADD PRIMARY KEY (`ar_id`);

--
-- Индексы таблицы `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`bk_id`);

--
-- Индексы таблицы `books_authors`
--
ALTER TABLE `books_authors`
  ADD PRIMARY KEY (`bkar_id`),
  ADD KEY `bkar_bk_fk` (`bkar_bk_id`),
  ADD KEY `bkar_ar_fk` (`bkar_ar_id`);

--
-- Индексы таблицы `books_genres`
--
ALTER TABLE `books_genres`
  ADD PRIMARY KEY (`bkge_id`);

--
-- Индексы таблицы `books_sequence`
--
ALTER TABLE `books_sequence`
  ADD PRIMARY KEY (`bkse_id`);

--
-- Индексы таблицы `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`ge_id`);

--
-- Индексы таблицы `genres_groups`
--
ALTER TABLE `genres_groups`
  ADD PRIMARY KEY (`gg_id`),
  ADD UNIQUE KEY `gg_title` (`gg_title`);

--
-- Индексы таблицы `sequence`
--
ALTER TABLE `sequence`
  ADD PRIMARY KEY (`se_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ur_id`),
  ADD UNIQUE KEY `login` (`ur_login`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `authors`
--
ALTER TABLE `authors`
  MODIFY `ar_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT для таблицы `books`
--
ALTER TABLE `books`
  MODIFY `bk_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT для таблицы `books_authors`
--
ALTER TABLE `books_authors`
  MODIFY `bkar_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=158;

--
-- AUTO_INCREMENT для таблицы `books_genres`
--
ALTER TABLE `books_genres`
  MODIFY `bkge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT для таблицы `books_sequence`
--
ALTER TABLE `books_sequence`
  MODIFY `bkse_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `genres`
--
ALTER TABLE `genres`
  MODIFY `ge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `genres_groups`
--
ALTER TABLE `genres_groups`
  MODIFY `gg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `sequence`
--
ALTER TABLE `sequence`
  MODIFY `se_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `ur_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `books_authors`
--
ALTER TABLE `books_authors`
  ADD CONSTRAINT `bkar_ar_fk` FOREIGN KEY (`bkar_ar_id`) REFERENCES `authors` (`ar_id`),
  ADD CONSTRAINT `bkar_bk_fk` FOREIGN KEY (`bkar_bk_id`) REFERENCES `books` (`bk_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
