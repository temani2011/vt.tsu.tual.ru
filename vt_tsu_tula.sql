-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 27 2019 г., 17:45
-- Версия сервера: 5.6.43
-- Версия PHP: 7.1.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `vt_tsu_tula`
--

-- --------------------------------------------------------

--
-- Структура таблицы `attendance`
--

CREATE TABLE `attendance` (
  `uid` int(11) NOT NULL,
  `sid` int(11) NOT NULL,
  `date` date NOT NULL,
  `score` int(11) NOT NULL,
  `attend` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `attendance`
--

INSERT INTO `attendance` (`uid`, `sid`, `date`, `score`, `attend`) VALUES
(1, 1, '2019-02-07', 2, 1),
(3, 1, '2019-02-07', 1, 0),
(6, 1, '2019-02-07', 0, 0),
(1, 1, '2019-01-31', 5, 1),
(1, 1, '2019-05-16', 2, 1),
(3, 1, '2019-05-16', 0, 0),
(6, 1, '2019-05-16', 4, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `blogs`
--

CREATE TABLE `blogs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `blogs`
--

INSERT INTO `blogs` (`id`, `user_id`, `description`) VALUES
(1, 1, ''),
(2, 2, ''),
(3, 18, '');

-- --------------------------------------------------------

--
-- Структура таблицы `catalogs`
--

CREATE TABLE `catalogs` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `cat_role` enum('student','teacher','staff','') NOT NULL,
  `cat_rwgrant` enum('r','rw','','') NOT NULL,
  `cat_path` varchar(255) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `catalogs`
--

INSERT INTO `catalogs` (`id`, `title`, `cat_role`, `cat_rwgrant`, `cat_path`, `user_id`) VALUES
(1, 'Параллельное программирование', 'student', 'r', '/docs/Параллельное программирование', 2),
(2, 'ООП', 'student', 'r', '/docs/ООП', 4),
(3, 'Лабораторные ПП гр.220251 ', 'student', 'rw', '/docs/Лабораторные ПП гр.220251', 2),
(24, 'новый каталог', 'student', 'rw', '/docs/новый каталог', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_where` int(11) NOT NULL,
  `text` varchar(255) NOT NULL,
  `date_c` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`id`, `id_user`, `id_where`, `text`, `date_c`) VALUES
(1, 2, 3, 'asdfadf', '2019-04-08 10:44:59'),
(2, 2, 3, 'agasg', '2019-04-08 10:50:43'),
(3, 2, 4, 'asdga', '2019-04-08 10:50:56'),
(4, 2, 3, 'dfafga', '2019-04-08 10:51:07'),
(5, 2, 3, 'dfafgaasdgsadgsag', '2019-04-08 10:51:11'),
(6, 1, 8, 'dfsgsfg', '2019-04-21 10:54:44'),
(7, 1, 8, 'adfa', '2019-04-28 10:46:31');

-- --------------------------------------------------------

--
-- Структура таблицы `dialogs`
--

CREATE TABLE `dialogs` (
  `id` int(11) NOT NULL,
  `title` varchar(64) NOT NULL,
  `cover` varchar(64) NOT NULL,
  `creator_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `dialogs`
--

INSERT INTO `dialogs` (`id`, `title`, `cover`, `creator_id`) VALUES
(1, 'Группа 220251', '', 2),
(2, 'Группа 220651', '', 4),
(51, 'доклады', '', 1),
(52, 'Никулин Артем Александрович-Суворов Алексей Петрович', '', 1),
(57, 'Никулин Артем Александрович-Петров Василий Сергеевич', '', 1),
(58, 'Волков Александр Николаевич-Фамусов Виктор Олегович', '', 18);

-- --------------------------------------------------------

--
-- Структура таблицы `docs`
--

CREATE TABLE `docs` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` enum('student','teacher','staff') NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_catalog` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `docs`
--

INSERT INTO `docs` (`id`, `name`, `role`, `id_user`, `id_catalog`) VALUES
(1, 'Методичка лабы.docx', 'student', 2, 2),
(2, 'Методичка КР.pdf', 'teacher', 2, 2),
(7, 'ПП. Диаграммы.xlsx', 'student', 4, 1),
(8, 'ПП.Лабараторные.docx', 'student', 4, 1),
(9, 'ПП.Лекция 1.pptx', 'student', 4, 1),
(10, 'Программы группа220412.rar', 'teacher', 4, 1),
(11, 'asdasd.txt', 'student', 1, 3),
(19, '234.rar', 'student', 6, 3),
(22, 'NewBands.xml', 'student', 1, 3),
(34, 'sdfsdf.asm', 'teacher', 2, 24),
(35, 'йуа.jpg', 'teacher', 2, 24),
(38, '1.docx', 'teacher', 2, 24),
(39, '1LTBw5LlafQ.jpg', 'student', 1, 24),
(40, 'Лабораторная работа №7.docx', 'teacher', 2, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `groups`
--

CREATE TABLE `groups` (
  `id` int(11) NOT NULL,
  `group_number` varchar(20) NOT NULL,
  `specialty` varchar(60) NOT NULL,
  `description` text NOT NULL,
  `id_subfaculty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `groups`
--

INSERT INTO `groups` (`id`, `group_number`, `specialty`, `description`, `id_subfaculty`) VALUES
(1, '220251', 'Вычислительные машины, комплексы, системы и сети', '0', 1),
(2, '220651', 'Программное обеспечение', '', 1),
(4, '222651', 'САПР', '', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `imgs`
--

CREATE TABLE `imgs` (
  `id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `date` timestamp NOT NULL,
  `path` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `imgs`
--

INSERT INTO `imgs` (`id`, `description`, `date`, `path`, `id_user`) VALUES
(1, 'dfhsdfhsdfhs', '2019-02-17 21:00:00', '/img/2/7YeOfFqbvcc.jpg', 2),
(2, 'srgashadfh', '2019-02-17 21:00:00', '/img/2/HnNlLoARJNw.jpg', 2),
(3, 'arharhahr', '2019-02-17 21:00:00', '/img/2/IllRz5G9TE0.jpg', 2),
(4, 'ahhhhhhhhhhhhhhhhhh', '2019-02-17 21:00:00', '/img/2/pmibM1-hIoI.jpg', 2),
(7, '', '2019-04-08 12:20:59', '/img/2/DT06027.JPG', 2),
(8, '', '2019-04-21 10:54:24', '/img/1/img00032.gif', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `likes`
--

CREATE TABLE `likes` (
  `id` int(11) NOT NULL,
  `id_img` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `likes`
--

INSERT INTO `likes` (`id`, `id_img`, `id_user`, `date`) VALUES
(12, 4, 2, '2019-04-01 15:53:47'),
(14, 3, 2, '2019-04-01 15:56:02'),
(16, 2, 2, '2019-04-08 13:04:21'),
(37, 8, 1, '2019-04-28 13:39:21'),
(38, 1, 2, '2019-05-12 16:16:57');

-- --------------------------------------------------------

--
-- Структура таблицы `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `text` text NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_read` tinyint(1) NOT NULL,
  `role` enum('student','teacher','staff','') NOT NULL,
  `sender_id` int(11) NOT NULL,
  `dialog_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `messages`
--

INSERT INTO `messages` (`id`, `text`, `date`, `is_read`, `role`, `sender_id`, `dialog_id`) VALUES
(1, 'Здравствуйте, студенты!', '2019-01-14 13:57:40', 0, '', 2, 1),
(2, 'Проверка работы диалогов.', '2019-01-14 13:58:40', 0, '', 2, 1),
(44, 'sdfsd', '2019-01-16 18:30:00', 0, '', 1, 1),
(46, 'sdfs', '2019-01-16 18:30:55', 0, '', 3, 1),
(48, 'sdfs', '2019-01-16 18:31:24', 0, '', 1, 1),
(50, 'sdf', '2019-01-16 18:31:40', 0, '', 3, 1),
(51, 'df', '2019-01-16 18:31:49', 0, '', 1, 1),
(53, 'Ну вроде работает', '2019-01-16 18:31:58', 0, '', 3, 1),
(55, 'угу', '2019-01-16 18:32:03', 0, '', 1, 1),
(57, 'Надеюсь', '2019-01-16 18:32:06', 0, '', 1, 1),
(59, 'Ураа', '2019-01-16 18:32:34', 0, '', 3, 1),
(61, 'Вернулс', '2019-01-16 18:32:43', 0, '', 1, 1),
(62, 'Здравствуйте!', '2019-01-17 17:45:24', 0, '', 1, 52),
(65, 'system: Никулин Артем Александрович cоздал диалог доклады', '2019-01-18 13:09:25', 1, '', 1, 51),
(66, 'ываы', '2019-01-18 13:09:51', 0, '', 1, 51),
(68, 'слушаю', '2019-01-19 14:21:20', 0, '', 6, 51),
(70, 'так', '2019-01-19 14:23:28', 0, '', 6, 51),
(72, 'стоп что', '2019-01-19 14:23:57', 0, '', 1, 51),
(74, 'ну вот ', '2019-01-19 14:24:09', 0, '', 6, 51),
(76, 'а почему было два сообщения', '2019-01-19 14:24:22', 0, '', 6, 51),
(78, 'не знаю', '2019-01-19 14:24:28', 0, '', 1, 51),
(83, 'текст', '2019-01-28 13:58:47', 0, '', 1, 1),
(84, 'проверка', '2019-01-28 13:59:05', 0, '', 2, 1),
(88, 'Здравствуйте, нужно ли заполнять бланки', '2019-01-28 14:07:25', 0, '', 1, 57),
(89, 'lf', '2019-01-28 14:11:40', 0, '', 2, 57),
(90, 'да', '2019-01-28 14:11:43', 0, '', 2, 57),
(91, 'понял', '2019-01-28 14:11:56', 0, '', 1, 57),
(156, '<p>gsdagwa<img src=\"../img/2/7YeOfFqbvcc.jpg\" alt=\"7YeOfFqbvcc.jpg\" width=\"100\" height=\"56\" /></p>', '2019-02-18 11:53:18', 0, '', 2, 57),
(157, '<p>gsdagwa <a href=\"../docs/ООП/Методичка лабы.docx\">Методичка лабы.docx</a></p>', '2019-02-18 11:53:33', 0, '', 2, 57),
(159, '<p>asdasf <img src=\"https://cloud.tinymce.com/stable/plugins/emoticons/img/smiley-embarassed.gif\" alt=\"embarassed\" /></p>', '2019-02-18 12:04:43', 0, '', 2, 57),
(160, '', '2019-05-16 16:12:38', 0, '', 18, 58),
(161, 'uhuh', '2019-05-16 16:17:05', 0, '', 18, 58),
(162, 'asdfsd', '2019-05-16 16:18:25', 0, '', 25, 58),
(163, 'dsfg', '2019-05-16 16:18:30', 0, '', 18, 58),
(164, 'd', '2019-05-16 16:18:38', 0, '', 25, 58),
(165, 'd', '2019-05-16 16:18:39', 0, '', 25, 58);

-- --------------------------------------------------------

--
-- Структура таблицы `news`
--

CREATE TABLE `news` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `text` text NOT NULL,
  `date` date NOT NULL,
  `imgs` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `news`
--

INSERT INTO `news` (`id`, `user_id`, `name`, `text`, `date`, `imgs`) VALUES
(1, 1, 'BBuilt card', 'We’ve created this admin panel for everyone who wants to create any templates based on our ready components. Our mission is to deliver a user-friendly, clear and easy administration panel, that can be used by both, simple websites and sophisticated systems. The only requirement is a basic HTML and CSS knowledge—as a reward, you’ll be able to manage and visualize different types of data in the easiest possible way!', '2019-01-04', '/img/news/pmibM1-hIoI.jpg /img/news/IllRz5G9TE0.jpg'),
(2, 1, 'Card blue', 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aperiam deleniti fugit incidunt, iste, itaque minima neque pariatur perferendis sed suscipit velit vitae voluptatem. A consequuntur, deserunt eaque error nulla temporibus!', '2019-01-05', '/img/news/pmibM1-hIoI.jpg'),
(3, 1, 'фываыфва', 'фыввввввввввввввввввввввввввввввввввввввввввввввввввввввввввфыввввввввввввввввввввввввввввввввввввввввввввввввввввввввввфыввввввввввввввввввввввввввввввввввввввввввввввввввввввввввфыввввввввввввввввввввввввввввввввввввввввввввввввввввввввввфывввввввввввввввввввввввввввввввввввввввввввввввввввввввввв', '2019-01-04', ''),
(4, 1, 'фывфывфывфывфпц3й4п', 'фывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4п', '2019-01-02', ''),
(5, 1, '66666666фываыф', 'фывввввввввввввввввввввввввввввв333333333333333333вввввввввввввввввввввввввввввввввввввввввввввввввввввфыввввввввввввввввввввввввввввввввввввввввввввввввввввввввввфыввввввввввввввввввввввввввввввввввввввввввввввввввввввввввфывввввввввввввввввввввввввввввввввввввввввввввввввввввввввв', '2019-01-05', ''),
(6, 1, 'фывф4444444444ывфпц3й4п', 'фывфывфывфывфпц3й4пфывфывфывфывфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4пфывфывфывфывфпц3й4п', '2019-01-04', ''),
(13, 2, 'gdfgdfgd', '<ul>\n<li><a href=\"../docs/ООП/Методичка КР.pdf\">Методичка КР.pdf</a></li>\n<li><a href=\"../docs/новый каталог/йуа.jpg\">йуа.jpg</a></li>\n<li><a href=\"../docs/ООП/Методичка лабы.docx\">Методичка лабы.docx</a></li>\n</ul>', '2019-02-14', ''),
(15, 2, 'sdf', '<p>asdaagsdasdaagsdasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaa<strong><em>gsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaagsasdaags</em></strong></p>\n<p><code>asfafsasfa</code></p>\n<p><img style=\"display: block; margin-left: auto; margin-right: auto;\" src=\"../img/2/HnNlLoARJNw.jpg\" alt=\"\" width=\"300\" height=\"168\" /></p>', '2019-02-14', '');

-- --------------------------------------------------------

--
-- Структура таблицы `participants`
--

CREATE TABLE `participants` (
  `did` int(11) NOT NULL,
  `role` enum('student','teacher','staff','') NOT NULL,
  `uid` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `participants`
--

INSERT INTO `participants` (`did`, `role`, `uid`) VALUES
(1, '', 1),
(1, '', 2),
(1, '', 3),
(2, '', 4),
(2, '', 5),
(1, '', 6),
(51, '', 1),
(52, '', 3),
(52, '', 1),
(57, '', 2),
(57, '', 1),
(51, '', 3),
(51, '', 6),
(51, '', 2),
(58, '', 25),
(58, '', 18);

-- --------------------------------------------------------

--
-- Структура таблицы `posts`
--

CREATE TABLE `posts` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `text` text NOT NULL,
  `date` timestamp NOT NULL,
  `blog_id` int(11) NOT NULL,
  `com_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `posts`
--

INSERT INTO `posts` (`id`, `name`, `text`, `date`, `blog_id`, `com_id`) VALUES
(3, 'sdfgsfdg', '<p><strong>sadfgsdgsadsadgsadgsadgsadg</strong></p>\n<p><strong>sadgsadgsadgsadgsadg</strong></p>\n<p><a href=\"../docs/ООП/Лабораторная работа №7.docx\">Лабораторная работа №7.docx</a></p>\n<p><img src=\"../img/2/DT06027.JPG\" alt=\"DT06027.JPG\" width=\"140\" height=\"140\" /></p>', '2019-04-14 21:00:00', 2, 0),
(4, 'dfgfgfgf', 'sadfgsdgsadghhh', '2019-04-14 21:00:00', 2, 213),
(8, 'asdasd', '<p>sgsdfgsdfgsdfgsdfgsdfg</p>\n<p><img src=\"../img/1/img00032.gif\" alt=\"img00032.gif\" width=\"556\" height=\"149\" /></p>', '2019-04-21 13:52:40', 1, 0),
(9, 'Новая публикация', '<p style=\"text-align: center;\">ТЕкстТЕ<em>кстТЕкстТЕкстТЕк<strong>стТЕкстТЕкстТЕкстТЕк</strong>стТЕкстТЕкстТЕкстТЕкстТЕкстТЕк</em>стТЕкстТЕкстТЕкстТЕкстм</p>', '2019-04-28 09:45:52', 1, 0);

-- --------------------------------------------------------

--
-- Структура таблицы `schedule`
--

CREATE TABLE `schedule` (
  `id` int(11) NOT NULL,
  `week` enum('odd','even','both','') NOT NULL,
  `day` int(11) NOT NULL,
  `pair` int(11) NOT NULL,
  `type` enum('lecture','practice','lab','seminar') NOT NULL,
  `corps` varchar(16) NOT NULL,
  `auditory` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `schedule`
--

INSERT INTO `schedule` (`id`, `week`, `day`, `pair`, `type`, `corps`, `auditory`, `subject_id`, `group_id`, `teacher_id`) VALUES
(1, 'odd', 3, 2, 'lecture', 'Гл.', 326, 1, 1, 2),
(2, 'both', 3, 5, 'practice', 'Гл.', 351, 1, 1, 2),
(3, 'odd', 3, 3, 'lecture', 'Гл.', 326, 2, 1, 4),
(4, 'even', 1, 2, 'lecture', 'Гл.', 326, 2, 1, 4),
(5, 'even', 3, 3, 'lab', 'Гл.', 351, 2, 1, 4),
(6, 'both', 2, 2, 'lecture', 'Гл.', 326, 2, 2, 4),
(7, 'both', 4, 3, 'lab', 'Гл.', 351, 1, 2, 2),
(8, 'both', 4, 1, 'lab', 'Гл.', 351, 1, 1, 2),
(10, 'both', 1, 1, 'lecture', '545', 123, 1, 4, 4),
(13, 'even', 1, 1, 'lecture', '9', 115, 1, 1, 2),
(14, 'odd', 1, 3, 'lecture', '132', 1, 1, 4, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `subfaculty`
--

CREATE TABLE `subfaculty` (
  `id` int(11) NOT NULL,
  `name` varchar(60) NOT NULL,
  `faculty` varchar(60) NOT NULL,
  `university` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `subfaculty`
--

INSERT INTO `subfaculty` (`id`, `name`, `faculty`, `university`) VALUES
(1, 'Кафедра вычислительной техники (ВТ)', 'Институт прикладной математики и компьютерных наук (ИПМКН)', 'Тульский государственный университет (ТулГУ)');

-- --------------------------------------------------------

--
-- Структура таблицы `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `short_name` varchar(32) NOT NULL,
  `hours` int(11) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `short_name`, `hours`, `description`) VALUES
(1, 'Объектно-ориентированное программирование', 'ООП', 140, ''),
(2, 'Параллельное программирование', 'ППрогр', 120, '');

-- --------------------------------------------------------

--
-- Структура таблицы `tokens`
--

CREATE TABLE `tokens` (
  `token` varchar(64) NOT NULL,
  `user_id` int(11) NOT NULL,
  `info` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `tokens`
--

INSERT INTO `tokens` (`token`, `user_id`, `info`) VALUES
('0891af894db9a0854b60dfa0f197b63a', 2, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:67.0) Gecko/20100101 Firefox/67.0');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `surname` varchar(24) NOT NULL,
  `name` varchar(24) NOT NULL,
  `midname` varchar(24) NOT NULL,
  `birth_date` date NOT NULL,
  `phone` varchar(20) NOT NULL,
  `email` varchar(32) NOT NULL,
  `password` varchar(64) NOT NULL,
  `city` varchar(32) NOT NULL,
  `date_of_registration` timestamp NOT NULL,
  `role` enum('student','teacher','staff','admin') NOT NULL,
  `img` text NOT NULL,
  `id_subfaculty` int(11) DEFAULT NULL,
  `id_groups` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `surname`, `name`, `midname`, `birth_date`, `phone`, `email`, `password`, `city`, `date_of_registration`, `role`, `img`, `id_subfaculty`, `id_groups`) VALUES
(1, 'Никулин', 'Артем', 'Александрович', '1997-10-04', '8(888)-888-88-88', 'tema.ni2011@yandex.ru', '202cb962ac59075b964b07152d234b70', 'Тула', '2019-01-01 21:00:00', 'student', '/img/users/depositphotos_54081723-stock-photo-beautiful-nature-landscape.jpg', 1, 1),
(2, 'Петров', 'Василий', 'Сергеевич', '1990-12-21', '8(800)-888-88-88', '32petrov23@list.ru', 'caf1a3dfb505ffed0d024130f58c5cfa', '', '2019-01-01 21:00:00', 'teacher', '/img/users/14.png', 1, NULL),
(3, 'Суворов', 'Алексей', 'Петрович', '1997-04-14', '8(800)-888-88-88', 'suvv12@lsit.ru', '202cb962ac59075b964b07152d234b70', 'Тула', '2019-01-07 21:00:00', 'student', '', 1, 1),
(4, 'Брулев', 'Федор', 'Михайлович', '2019-01-08', '8(800)-888-88-88', 'brerew@list.ru', 'caf1a3dfb505ffed0d024130f58c5cfa', 'Тула', '2019-01-13 21:00:00', 'teacher', '', 1, NULL),
(5, 'Фралов', 'Петр', 'Александрович', '1997-10-04', '8(800)-888-88-88', 'fralov22323@yandex.ru', '202cb962ac59075b964b07152d234b70', 'Тула', '2019-01-01 21:00:00', 'student', '', 1, 2),
(6, 'Быков', 'Павел', 'Петрович', '1997-04-14', '8(800)-888-88-88', 'biksdf@lsit.ru', '202cb962ac59075b964b07152d234b70', 'Тула', '2019-01-07 21:00:00', 'student', '', 1, 1),
(18, 'Волков', 'Александр', 'Николаевич', '0001-11-30', '8(888)-888-88-88', 'alexniv18@list.ru', '698D51A19D8A121CE581499D7B701668', 'Тула', '2019-01-25 11:49:45', 'staff', '/img/users/DT06027.JPG', 1, 1),
(25, 'Фамусов', 'Виктор', 'Олегович', '1997-04-03', '9(999)-999-99-99', 'bioxdsw97@mail.ru', '202cb962ac59075b964b07152d234b70', 'Серпухов', '2019-01-28 12:09:49', 'student', '', 1, 4),
(26, 'admin', '', '', '0000-00-00', '', 'tsuvtadmin', 'C6F057B86584942E415435FFB1FA93D4', '', '0000-00-00 00:00:00', 'admin', '', 1, NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `attendance`
--
ALTER TABLE `attendance`
  ADD KEY `sid` (`sid`),
  ADD KEY `uid` (`uid`);

--
-- Индексы таблицы `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`) USING BTREE;

--
-- Индексы таблицы `catalogs`
--
ALTER TABLE `catalogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_where` (`id_where`);

--
-- Индексы таблицы `dialogs`
--
ALTER TABLE `dialogs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creator_id` (`creator_id`);

--
-- Индексы таблицы `docs`
--
ALTER TABLE `docs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `id_catalog` (`id_catalog`);

--
-- Индексы таблицы `groups`
--
ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `group_number` (`group_number`),
  ADD KEY `id_subfaculty` (`id_subfaculty`);

--
-- Индексы таблицы `imgs`
--
ALTER TABLE `imgs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Индексы таблицы `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_img` (`id_img`),
  ADD KEY `id_user` (`id_user`);

--
-- Индексы таблицы `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `dialog_id` (`dialog_id`);

--
-- Индексы таблицы `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `participants`
--
ALTER TABLE `participants`
  ADD KEY `did` (`did`),
  ADD KEY `uid` (`uid`);

--
-- Индексы таблицы `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `blog_id` (`blog_id`);

--
-- Индексы таблицы `schedule`
--
ALTER TABLE `schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`),
  ADD KEY `subject_id` (`subject_id`),
  ADD KEY `professor_id` (`teacher_id`);

--
-- Индексы таблицы `subfaculty`
--
ALTER TABLE `subfaculty`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `tokens`
--
ALTER TABLE `tokens`
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_subfaculty` (`id_subfaculty`),
  ADD KEY `id_groups` (`id_groups`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `blogs`
--
ALTER TABLE `blogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `catalogs`
--
ALTER TABLE `catalogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `dialogs`
--
ALTER TABLE `dialogs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT для таблицы `docs`
--
ALTER TABLE `docs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT для таблицы `groups`
--
ALTER TABLE `groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `imgs`
--
ALTER TABLE `imgs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `likes`
--
ALTER TABLE `likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT для таблицы `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=166;

--
-- AUTO_INCREMENT для таблицы `news`
--
ALTER TABLE `news`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT для таблицы `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `schedule`
--
ALTER TABLE `schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT для таблицы `subfaculty`
--
ALTER TABLE `subfaculty`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`sid`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`uid`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `blogs`
--
ALTER TABLE `blogs`
  ADD CONSTRAINT `blogs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `catalogs`
--
ALTER TABLE `catalogs`
  ADD CONSTRAINT `catalogs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`id_where`) REFERENCES `imgs` (`id`);

--
-- Ограничения внешнего ключа таблицы `dialogs`
--
ALTER TABLE `dialogs`
  ADD CONSTRAINT `dialogs_ibfk_1` FOREIGN KEY (`creator_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `docs`
--
ALTER TABLE `docs`
  ADD CONSTRAINT `docs_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `docs_ibfk_2` FOREIGN KEY (`id_catalog`) REFERENCES `catalogs` (`id`);

--
-- Ограничения внешнего ключа таблицы `groups`
--
ALTER TABLE `groups`
  ADD CONSTRAINT `groups_ibfk_1` FOREIGN KEY (`id_subfaculty`) REFERENCES `subfaculty` (`id`);

--
-- Ограничения внешнего ключа таблицы `imgs`
--
ALTER TABLE `imgs`
  ADD CONSTRAINT `imgs_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`id_img`) REFERENCES `imgs` (`id`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `messages_ibfk_2` FOREIGN KEY (`dialog_id`) REFERENCES `dialogs` (`id`);

--
-- Ограничения внешнего ключа таблицы `news`
--
ALTER TABLE `news`
  ADD CONSTRAINT `news_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `participants_ibfk_2` FOREIGN KEY (`did`) REFERENCES `dialogs` (`id`),
  ADD CONSTRAINT `participants_ibfk_3` FOREIGN KEY (`uid`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`blog_id`) REFERENCES `blogs` (`id`);

--
-- Ограничения внешнего ключа таблицы `schedule`
--
ALTER TABLE `schedule`
  ADD CONSTRAINT `schedule_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `groups` (`id`),
  ADD CONSTRAINT `schedule_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`),
  ADD CONSTRAINT `schedule_ibfk_3` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `tokens`
--
ALTER TABLE `tokens`
  ADD CONSTRAINT `tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`id_subfaculty`) REFERENCES `subfaculty` (`id`),
  ADD CONSTRAINT `users_ibfk_2` FOREIGN KEY (`id_groups`) REFERENCES `groups` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
