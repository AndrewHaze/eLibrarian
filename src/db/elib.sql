-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Авг 16 2019 г., 09:29
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

DELIMITER $$
--
-- Функции
--
CREATE DEFINER=`root`@`%` FUNCTION `checkDoubles` (`f_bk_id` INT) RETURNS TINYINT(1) BEGIN 
  DECLARE cnt tinyint;
  DECLARE result tinyint;
  select count(1)
  into cnt
  from books b_new,
       books b_old
  where b_old.bk_doc_id = b_new.bk_doc_id
  	and b_old.bk_ur_id = b_new.bk_ur_id
    and b_old.bk_title = b_new.bk_title
    
    and (SELECT GROUP_CONCAT(bkar_ar_id ORDER BY bkar_ar_id SEPARATOR ",")
         FROM books_authors
         WHERE bkar_bk_id = b_old.bk_id) = 
         (SELECT GROUP_CONCAT(bkar_ar_id ORDER BY bkar_ar_id SEPARATOR ",")
         FROM books_authors
         WHERE bkar_bk_id = f_bk_id)
    
    
    and b_new.bk_id = f_bk_id
    and b_old.bk_id != f_bk_id;
    
    if cnt > 0 then
    set result = 1;
  end if;   
  
  RETURN result;
END$$

CREATE DEFINER=`root`@`%` FUNCTION `delAuthor` (`id` INT, `check` VARCHAR(3)) RETURNS TINYINT(1) BEGIN
  declare v_cnt int;
  DECLARE result tinyint;
  DECLARE done INT DEFAULT FALSE;
  DECLARE a, b INT;
  DECLARE cur1 CURSOR FOR
    SELECT bkar_id, bkar_bk_id
    FROM books_authors
    WHERE bkar_ar_id = ID;
  DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

  OPEN cur1;

  read_loop: LOOP
    FETCH cur1 INTO a, b;
    IF done THEN
      LEAVE read_loop;
    END IF;
    -- ищем соавтора
    set v_cnt = 0;
    select count(1)
    into v_cnt
    from books_authors
    where bkar_bk_id = B
      and bkar_ar_id != ID;
    -- если нет
    IF v_cnt = 0 THEN
      delete from books_genres
      where bkge_bk_id = B;
      delete from books_authors
      where bkar_id = A;
      delete from books
      where bk_id = b;
    END IF;
  END LOOP;

  CLOSE cur1;
  delete from authors
  where ar_id = ID;
  if v_cnt = 0 then
    set result = 1;
  else
    set result = 0;
  end if;
  return result;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Структура таблицы `authors`
--

CREATE TABLE `authors` (
  `ar_id` int(11) NOT NULL,
  `ar_owner` int(11) NOT NULL COMMENT 'user id (ur_id)',
  `ar_first_name` varchar(255) DEFAULT NULL,
  `ar_middle_name` varchar(255) DEFAULT NULL,
  `ar_last_name` varchar(255) NOT NULL,
  `ar_penname` varchar(256) NOT NULL,
  `ar_photo` longblob NOT NULL,
  `ar_email` varchar(256) NOT NULL,
  `ar_site` varchar(256) NOT NULL,
  `ar_biography` text NOT NULL,
  `ar_bibliography` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `books`
--

CREATE TABLE `books` (
  `bk_id` int(11) NOT NULL,
  `bk_ur_id` int(11) NOT NULL,
  `bk_se_id` int(11) NOT NULL,
  `bk_number` int(11) NOT NULL,
  `bk_added` date DEFAULT NULL,
  `bk_open` date DEFAULT NULL,
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
(12, 1, 'sf', 'Научная Фантастика'),
(13, 2, 'det_classic', 'Классический детектив'),
(14, 2, 'det_police', 'Полицейский детектив'),
(15, 2, 'det_action', 'Боевик'),
(16, 2, 'det_irony', 'Иронический детектив'),
(17, 2, 'det_history', 'Исторический детектив'),
(18, 2, 'det_espionage', 'Шпионский детектив'),
(19, 2, 'det_crime', 'Криминальный детектив'),
(20, 2, 'det_political', 'Политический детектив'),
(21, 2, 'det_maniac', 'Маньяки'),
(22, 2, 'det_hard', 'Крутой детектив'),
(23, 2, 'thriller', 'Триллер'),
(24, 2, 'detective', 'Детектив прочее (то, что не вошло в другие категории)'),
(25, 3, 'prose_classic', 'Классическая проза'),
(26, 3, 'prose_history', 'Историческая проза'),
(27, 3, 'prose_contemporary', 'Современная проза'),
(28, 3, 'prose_counter', 'Контркультура'),
(29, 3, 'prose_rus_classic', 'Русская классическая проза'),
(30, 3, 'prose_su_classics', 'Советская классическая проза'),
(31, 4, 'love_contemporary', 'Современные любовные романы'),
(32, 4, 'love_history', 'Исторические любовные романы'),
(33, 4, 'love_detective', 'Остросюжетные любовные романы'),
(34, 4, 'love_short', 'Короткие любовные романы'),
(35, 4, 'love_erotica', 'Эротика'),
(36, 5, 'adv_western', 'Вестерн'),
(37, 5, 'adv_history', 'Исторические приключения'),
(38, 5, 'adv_indian', 'Приключения про индейцев'),
(39, 5, 'adv_maritime', 'Морские приключения'),
(40, 5, 'adv_geo', 'Путешествия и география'),
(41, 5, 'adv_animal', 'Природа и животные'),
(42, 5, 'adventure', 'Прочие приключения (то, что не вошло в другие категории)'),
(43, 6, 'child_tale', 'Сказка'),
(44, 6, 'child_verse', 'Детские стихи'),
(45, 6, 'child_prose', 'Детскиая проза'),
(46, 6, 'child_sf', 'Детская фантастика'),
(47, 6, 'child_det', 'Детские остросюжетные'),
(48, 6, 'child_adv', 'Детские приключения'),
(49, 6, 'child_education', 'Детская образовательная литература'),
(50, 6, 'children', 'Прочая детская литература (то, что не вошло в другие категории)'),
(51, 7, 'poetry', 'Поэзия'),
(52, 7, 'dramaturgy', 'Драматургия'),
(53, 8, 'antique_ant', 'Античная литература'),
(54, 8, 'antique_european', 'Европейская старинная литература'),
(55, 8, 'antique_russian', 'Древнерусская литература'),
(56, 8, 'antique_east', 'Древневосточная литература'),
(57, 8, 'antique_myths', 'Мифы. Легенды. Эпос'),
(58, 8, 'antique', 'Прочая старинная литература (то, что не вошло в другие категории)'),
(59, 9, 'sci_history', 'История'),
(60, 9, 'sci_psychology', 'Психология'),
(61, 9, 'sci_culture', 'Культурология'),
(62, 9, 'sci_religion', 'Религиоведение'),
(63, 9, 'sci_philosophy', 'Философия'),
(64, 9, 'sci_politics', 'Политика'),
(65, 9, 'sci_business', 'Деловая литература'),
(66, 9, 'sci_juris', 'Юриспруденция'),
(67, 9, 'sci_linguistic', 'Языкознание'),
(68, 9, 'sci_medicine', 'Медицина'),
(69, 9, 'sci_phys', 'Физика'),
(70, 9, 'sci_math', 'Математика'),
(71, 9, 'sci_chem', 'Химия'),
(72, 9, 'sci_biology', 'Биология'),
(73, 9, 'sci_tech', 'Технические науки'),
(74, 9, 'science', 'Прочая научная литература (то, что не вошло в другие категории)'),
(75, 10, 'comp_www', 'Интернет'),
(76, 10, 'comp_programming', 'Программирование'),
(77, 10, 'comp_hard', 'Компьютерное \"Железо\"'),
(78, 10, 'comp_soft', 'Программы'),
(79, 10, 'comp_db', 'Базы данных'),
(80, 10, 'comp_osnet', 'ОС и Сети'),
(81, 10, 'computers', 'Прочая околокомпьтерная литература (то, что не вошло в другие категории)'),
(82, 11, 'ref_encyc', 'Энциклопедии'),
(83, 11, 'ref_dict', 'Словари'),
(84, 11, 'ref_ref', 'Справочники'),
(85, 11, 'ref_guide', 'Руководства'),
(86, 11, 'reference', 'Прочая справочная литература (то, что не вошло в другие категории)'),
(87, 12, 'nonf_biography', 'Биографии и Мемуары'),
(88, 12, 'nonf_publicism', 'Публицистика'),
(89, 12, 'nonf_criticism', 'Критика'),
(90, 12, 'design', 'Искусство и Дизайн'),
(91, 12, 'nonfiction', 'Прочая документальная литература (то, что не вошло в другие категории)'),
(92, 13, 'religion_rel', 'Религия'),
(93, 13, 'religion_esoterics', 'Эзотерика'),
(94, 13, 'religion_self', 'Самосовершенствование'),
(95, 13, 'religion', 'Прочая религионая литература (то, что не вошло в другие категории)'),
(96, 14, 'humor_anecdote', 'Анекдоты'),
(97, 14, 'humor_prose', 'Юмористическая проза'),
(98, 14, 'humor_verse', 'Юмористические стихи'),
(99, 14, 'humor', 'Прочий юмор (то, что не вошло в другие категории)'),
(100, 15, 'home_cooking', 'Кулинария'),
(101, 15, 'home_pets', 'Домашние животные'),
(102, 15, 'home_crafts', 'Хобби и ремесла'),
(103, 15, 'home_entertain', 'Развлечения'),
(104, 15, 'home_health', 'Здоровье'),
(105, 15, 'home_garden', 'Сад и огород'),
(106, 15, 'home_diy', 'Сделай сам'),
(107, 15, 'home_sport', 'Спорт'),
(108, 15, 'home_sex', 'Эротика, Секс');

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
-- Структура таблицы `languages`
--

CREATE TABLE `languages` (
  `lg_id` varchar(2) NOT NULL,
  `lg_name` varchar(36) DEFAULT NULL,
  `lg_cd3` varchar(3) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `languages`
--

INSERT INTO `languages` (`lg_id`, `lg_name`, `lg_cd3`) VALUES
('aa', 'Афарский', 'aar'),
('ab', 'Абхазский', 'abk'),
('ae', 'Авестийский', 'ave'),
('af', 'Африкаанс', 'afr'),
('ak', 'Акан', 'aka'),
('am', 'Амхарский', 'amh'),
('an', 'Арагонский', 'arg'),
('ar', 'Арабский', 'ara'),
('as', 'Ассамский', 'asm'),
('av', 'Аварский', 'ava'),
('ay', 'Аймара', 'aym'),
('az', 'Азербайджанский', 'aze'),
('ba', 'Башкирский', 'bak'),
('be', 'Белорусский', 'bel'),
('bg', 'Болгарский', 'bul'),
('bh', 'Бихарский', 'bih'),
('bi', 'Бислама', 'bis'),
('bm', 'Бамбара', 'bam'),
('bn', 'Бенгальский', 'ben'),
('bo', 'Тибетский', 'bod'),
('br', 'Бретонский', 'bre'),
('bs', 'Боснийский', 'bos'),
('ca', 'Каталонский', 'cat'),
('ce', 'Чеченский', 'che'),
('ch', 'Чаморро', 'cha'),
('co', 'Корсиканский', 'cos'),
('cr', 'Кри', 'cre'),
('cs', 'Чешский', 'ces'),
('cu', 'Церковнославянский (старославянский)', 'chu'),
('cv', 'Чувашский', 'chv'),
('cy', 'Валлийский', 'cym'),
('da', 'Датский', 'dan'),
('de', 'Немецкий', 'deu'),
('dv', 'Дивехи (Мальдивский)', 'div'),
('dz', 'Дзонг-кэ', 'dzo'),
('ee', 'Эве', 'ewe'),
('el', 'Греческий (новогреческий)', 'ell'),
('en', 'Английский', 'eng'),
('eo', 'Эсперанто', 'epo'),
('es', 'Испанский', 'spa'),
('et', 'Эстонский', 'est'),
('eu', 'Баскский', 'eus'),
('fa', 'Персидский', 'fas'),
('ff', 'Фулах', 'ful'),
('fi', 'Финский', 'fin'),
('fj', 'Фиджи', 'fij'),
('fo', 'Фарерский', 'fao'),
('fr', 'Французский', 'fra'),
('fy', 'Фризский', 'fry'),
('ga', 'Ирландский', 'gle'),
('gd', 'Гальский', 'gla'),
('gl', 'Галисийский', 'glg'),
('gn', 'Гуарани', 'grn'),
('gu', 'Гуджарати', 'guj'),
('gv', 'Мэнский', 'glv'),
('ha', 'Хауса', 'hau'),
('he', 'Иврит (современный)', 'heb'),
('hi', 'Хинди', 'hin'),
('ho', 'Хиримоту', 'hmo'),
('hr', 'Хорватский', 'hrv'),
('ht', 'Гаитянский креольский', 'hat'),
('hu', 'Венгерский', 'hun'),
('hy', 'Армянский', 'hye'),
('hz', 'Гереро', 'her'),
('ia', 'Интерлингва', 'ina'),
('id', 'Индонезийский', 'ind'),
('ie', 'Интерлингве', 'ile'),
('ig', 'Игбо', 'ibo'),
('ii', 'Носу', 'iii'),
('ik', 'Инупиак', 'ipk'),
('io', 'Идо', 'ido'),
('is', 'Исландский', 'isl'),
('it', 'Итальянский', 'ita'),
('iu', 'Инуктитут', 'iku'),
('ja', 'Японский', 'jpn'),
('jv', 'Яванский', 'jav'),
('ka', 'Грузинский', 'kat'),
('kg', 'Конго', 'kon'),
('ki', 'Кикуйю', 'kik'),
('kj', 'Киньяма', 'kua'),
('kk', 'Казахский', 'kaz'),
('kl', 'Гренландский', 'kal'),
('km', 'Кхмерский', 'khm'),
('kn', 'Каннада', 'kan'),
('ko', 'Корейский', 'kor'),
('kr', 'Канури', 'kau'),
('ks', 'Кашмири', 'kas'),
('ku', 'Курдский', 'kur'),
('kv', 'Коми', 'kom'),
('kw', 'Корнский', 'cor'),
('ky', 'Киргизский', 'kir'),
('la', 'Латинский', 'lat'),
('lb', 'Люксембургский', 'ltz'),
('lg', 'Ганда', 'lug'),
('li', 'Лимбургский', 'lim'),
('ln', 'Лингала', 'lin'),
('lo', 'Лаосский', 'lao'),
('lt', 'Литовский', 'lit'),
('lu', 'Луба-катанга', 'lub'),
('lv', 'Латвийский', 'lav'),
('mg', 'Малагасийский', 'mlg'),
('mh', 'Маршалльский', 'mah'),
('mi', 'Маори', 'mri'),
('mk', 'Македонский', 'mkd'),
('ml', 'Малаялам', 'mal'),
('mn', 'Монгольский', 'mon'),
('mr', 'Маратхи', 'mar'),
('ms', 'Малайский', 'msa'),
('mt', 'Мальтийский', 'mlt'),
('my', 'Бирманский', 'mya'),
('na', 'Науру', 'nau'),
('nb', 'Норвежский (букмол)', 'nob'),
('nd', 'Ндебеле северный', 'nde'),
('ne', 'Непальский', 'nep'),
('ng', 'Ндунга', 'ndo'),
('nl', 'Голландский', 'nld'),
('nn', 'Норвежский (нюнорск)', 'nno'),
('no', 'Норвежский', 'nor'),
('nr', 'Ндебеле южный', 'nbl'),
('nv', 'Навахо', 'nav'),
('ny', 'Ньянджа', 'nya'),
('oc', 'Окситанский', 'oci'),
('oj', 'Оджибве', 'oji'),
('om', 'Оромо', 'orm'),
('or', 'Ория', 'ori'),
('os', 'Осетинский', 'oss'),
('pa', 'Пенджабский', 'pan'),
('pi', 'Пали', 'pli'),
('pl', 'Польский', 'pol'),
('ps', 'Пушту', 'pus'),
('pt', 'Португальский', 'por'),
('qu', 'Кечуа', 'que'),
('rm', 'Ретороманский', 'roh'),
('rn', 'Рунди', 'run'),
('ro', 'Румынский', 'ron'),
('ru', 'Русский', 'rus'),
('rw', 'Руанда', 'kin'),
('sa', 'Санскрит', 'san'),
('sc', 'Сардинский', 'srd'),
('sd', 'Синдхи', 'snd'),
('se', 'Северносаамский', 'sme'),
('sg', 'Санго', 'sag'),
('si', 'Сингальский', 'sin'),
('sk', 'Словацкий', 'slk'),
('sl', 'Словенский', 'slv'),
('sm', 'Самоанский', 'smo'),
('sn', 'Шона', 'sna'),
('so', 'Сомали', 'som'),
('sq', 'Албанский', 'sqi'),
('sr', 'Сербский', 'srp'),
('ss', 'Свати', 'ssw'),
('st', 'Сото южный', 'sot'),
('su', 'Сунданский', 'sun'),
('sv', 'Шведский', 'swe'),
('sw', 'Суахили', 'swa'),
('ta', 'Тамильский', 'tam'),
('te', 'Телугу', 'tel'),
('tg', 'Таджикский', 'tgk'),
('th', 'Тайский', 'tha'),
('ti', 'Тигринья', 'tir'),
('tk', 'Туркменский', 'tuk'),
('tl', 'Тагальский', 'tgl'),
('tn', 'Тсвана', 'tsn'),
('to', 'Тонганский', 'ton'),
('tr', 'Турецкий', 'tur'),
('ts', 'Тсонга', 'tso'),
('tt', 'Татарский', 'tat'),
('tw', 'Тви', 'twi'),
('ty', 'Таитянский', 'tah'),
('ug', 'Уйгурский', 'uig'),
('uk', 'Украинский', 'ukr'),
('ur', 'Урду', 'urd'),
('uz', 'Узбекский', 'uzb'),
('ve', 'Венда', 'ven'),
('vi', 'Вьетнамский', 'vie'),
('vo', 'Волапюк', 'vol'),
('wa', 'Валлонский', 'wln'),
('wo', 'Волоф', 'wol'),
('xh', 'Коса', 'xho'),
('yi', 'Идиш', 'yid'),
('yo', 'Йоруба', 'yor'),
('za', 'Чжуанский', 'zha'),
('zh', 'Китайский', 'zho'),
('zu', 'Зулу', 'zul');

-- --------------------------------------------------------

--
-- Структура таблицы `series`
--

CREATE TABLE `series` (
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
(1, 'qwerty', '$2y$10$aUvnivc6AVIoF4wMRd06AOk45/NN0RX0sIUxKaaUK56LhHmrPV1lu'),
(2, 'qaz', '$2y$10$IGDpEFci.272f2G3MQAvOeU6vcC7ceSJBa0p8dpTNLE1Qt0q5H/3a'),
(3, 'pupkin', '$2y$10$Ivei5yX16FYRkIsRMiWZfutyvQz7.ktUI5C.BM6B8nXeY1GCeMkJC'),
(4, 'aaa', '$2y$10$ujfGjGXEDCnD9g6UtM4SuuKdNBnO1bMeYn0R2eFGu6t2tcYz4vUYm'),
(5, 'qqq', '$2y$10$RqUtaH5Hu5TTByaXwgQ24O/NvOKMBnL96Q4UjHJOp2AeDM/7FCCd.');

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
  ADD PRIMARY KEY (`bk_id`),
  ADD KEY `bk_se_fk` (`bk_se_id`);

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
  ADD PRIMARY KEY (`bkge_id`),
  ADD KEY `bkge_bk_fk` (`bkge_bk_id`),
  ADD KEY `bkge_ge_fk` (`bkge_ge_id`);

--
-- Индексы таблицы `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`ge_id`),
  ADD KEY `ge_gg_fk` (`ge_gg_id`);

--
-- Индексы таблицы `genres_groups`
--
ALTER TABLE `genres_groups`
  ADD PRIMARY KEY (`gg_id`),
  ADD UNIQUE KEY `gg_title` (`gg_title`);

--
-- Индексы таблицы `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`lg_id`);

--
-- Индексы таблицы `series`
--
ALTER TABLE `series`
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
  MODIFY `ar_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `books`
--
ALTER TABLE `books`
  MODIFY `bk_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `books_authors`
--
ALTER TABLE `books_authors`
  MODIFY `bkar_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `books_genres`
--
ALTER TABLE `books_genres`
  MODIFY `bkge_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `genres`
--
ALTER TABLE `genres`
  MODIFY `ge_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT для таблицы `genres_groups`
--
ALTER TABLE `genres_groups`
  MODIFY `gg_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `series`
--
ALTER TABLE `series`
  MODIFY `se_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `ur_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `bk_se_fk` FOREIGN KEY (`bk_se_id`) REFERENCES `series` (`se_id`);

--
-- Ограничения внешнего ключа таблицы `books_authors`
--
ALTER TABLE `books_authors`
  ADD CONSTRAINT `bkar_ar_fk` FOREIGN KEY (`bkar_ar_id`) REFERENCES `authors` (`ar_id`),
  ADD CONSTRAINT `bkar_bk_fk` FOREIGN KEY (`bkar_bk_id`) REFERENCES `books` (`bk_id`);

--
-- Ограничения внешнего ключа таблицы `books_genres`
--
ALTER TABLE `books_genres`
  ADD CONSTRAINT `bkge_bk_fk` FOREIGN KEY (`bkge_bk_id`) REFERENCES `books` (`bk_id`),
  ADD CONSTRAINT `bkge_ge_fk` FOREIGN KEY (`bkge_ge_id`) REFERENCES `genres` (`ge_id`);

--
-- Ограничения внешнего ключа таблицы `genres`
--
ALTER TABLE `genres`
  ADD CONSTRAINT `ge_gg_fk` FOREIGN KEY (`ge_gg_id`) REFERENCES `genres_groups` (`gg_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
