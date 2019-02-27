-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Фев 27 2019 г., 09:14
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
-- Структура таблицы `books`
--

CREATE TABLE `books` (
  `bk_id` int(11) NOT NULL,
  `bk_ur_id` int(11) NOT NULL,
  `bk_se_id` int(11) NOT NULL,
  `bk_number` int(11) NOT NULL,
  `bk_doc_id` varchar(255) NOT NULL,
  `bk_title` varchar(255) NOT NULL,
  `bk_src_title` varchar(255) DEFAULT NULL,
  `bk_annotation` text,
  `bk_date` date DEFAULT NULL,
  `bk_lang` varchar(2) DEFAULT NULL,
  `bk_src_lang` varchar(3) DEFAULT NULL,
  `bk_file` varchar(255) DEFAULT NULL,
  `bk_cover` longblob,
  `bk_keywords` text,
  `bk_translators` text COMMENT 'переводчики',
  `bk_pub_title` varchar(255) DEFAULT NULL,
  `bk_pub_publisher` varchar(255) DEFAULT NULL,
  `bk_pub_city` varchar(255) DEFAULT NULL,
  `bk_pub_year` varchar(4) DEFAULT NULL,
  `bk_pub_isbn` varchar(255) DEFAULT NULL,
  `bk_doc_history` text,
  `bk_doc_date` date DEFAULT NULL,
  `bk_doc_ver` varchar(25) DEFAULT NULL,
  `bk_doc_authors` text,
  `bk_doc_programms` varchar(255) DEFAULT NULL,
  `bk_doc_url` varchar(255) DEFAULT NULL,
  `bk_doc_ocr_authors` varchar(255) DEFAULT NULL,
  `bk_doc_file_name` varchar(255) DEFAULT NULL,
  `bk_doc_file_date` date DEFAULT NULL,
  `bk_doc_file_size` int(11) DEFAULT NULL,
  `bk_doc_format` varchar(25) DEFAULT NULL,
  `bk_read` tinyint(1) DEFAULT '0',
  `bk_to_plan` tinyint(1) DEFAULT '0',
  `bk_favorites` tinyint(1) DEFAULT '0',
  `bk_stars` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`bk_id`),
  ADD KEY `bk_se_fk` (`bk_se_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `books`
--
ALTER TABLE `books`
  MODIFY `bk_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1206;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `bk_se_fk` FOREIGN KEY (`bk_se_id`) REFERENCES `series` (`se_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
