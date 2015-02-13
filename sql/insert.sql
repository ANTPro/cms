
;
DROP TABLE IF EXISTS `article`;
CREATE TABLE IF NOT EXISTS `article` (
  `key_article` bigint(20) NOT NULL auto_increment,
  `id_section` bigint(20) NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `content` text NOT NULL,
  `description` varchar(255) NOT NULL default '',
  `id_users` bigint(10) NOT NULL default '0',
  `pubdate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`key_article`),
  UNIQUE KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `department`;
CREATE TABLE IF NOT EXISTS `department` (
  `key_department` bigint(10) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `id_faculty` bigint(10) NOT NULL default '0',
  PRIMARY KEY  (`key_department`),
  UNIQUE KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `faculty`;
CREATE TABLE IF NOT EXISTS `faculty` (
  `key_faculty` bigint(10) NOT NULL auto_increment,
  `title` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`key_faculty`),
  UNIQUE KEY `faculty` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `groups`;
CREATE TABLE IF NOT EXISTS `groups` (
  `key_groups` bigint(10) NOT NULL auto_increment,
  `title` varchar(5) NOT NULL default '',
  `id_specialization` bigint(10) NOT NULL default '0',
  `year` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`key_groups`),
  UNIQUE KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `linkdepartment`;
CREATE TABLE IF NOT EXISTS `linkdepartment` (
  `key_linkdepartment` bigint(10) NOT NULL auto_increment,
  `id_users` bigint(10) NOT NULL default '0',
  `id_department` bigint(10) NOT NULL default '0',
  PRIMARY KEY  (`key_linkdepartment`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `linkgroups`;
CREATE TABLE IF NOT EXISTS `linkgroups` (
  `key_linkgroups` bigint(10) NOT NULL auto_increment,
  `id_users` bigint(10) NOT NULL default '0',
  `id_groups` bigint(10) NOT NULL default '0',
  PRIMARY KEY  (`key_linkgroups`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `mail`;
CREATE TABLE IF NOT EXISTS `mail` (
  `key_mail` bigint(10) NOT NULL auto_increment,
  `foruser` bigint(10) NOT NULL default '0',
  `id_messages` bigint(10) NOT NULL default '0',
  `isnew` enum('нет','да') NOT NULL default 'да',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`key_mail`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `key_comments` bigint(10) NOT NULL auto_increment,
  `page` varchar(20) NOT NULL default '',
  `key` bigint(10) NOT NULL default '0',
  `id_messages` bigint(10) NOT NULL default '0',
  PRIMARY KEY  (`key_comments`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `key_messages` bigint(10) NOT NULL auto_increment,
  `author` bigint(10) NOT NULL,
  `message` text NOT NULL,
  `date` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`key_messages`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `section`;
CREATE TABLE IF NOT EXISTS `section` (
  `key_section` bigint(10) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `parent` bigint(20) NOT NULL default '0',
  `level` int(10) NOT NULL default '0',
  PRIMARY KEY  (`key_section`),
  UNIQUE KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `specialization`;
CREATE TABLE IF NOT EXISTS `specialization` (
  `key_specialization` bigint(10) NOT NULL auto_increment,
  `title` varchar(255) NOT NULL default '',
  `id_department` bigint(10) NOT NULL default '0',
  PRIMARY KEY  (`key_specialization`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `article` (`key_article`, `id_section`, `title`, `content`, `description`, `id_users`, `pubdate`) VALUES
(1, 5, 'Комсомольская правда', '<div style="text-align: center;"><font style="font-weight: bold;" size="7"></font></div><font style="color: rgb(255, 0, 0); font-family: Arial;" size="7"><span style="font-weight: bold;">Л</span></font>есной институт – это образ жизни. Комсомольская правда в Коми.', 'Лесной институт – это образ жизни. Комсомольская правда в Коми.', 1, '2007-12-27 17:25:56'),
(3, 5, 'Научно-исследовательская работа', '<font size="7"><span style="color: rgb(255, 0, 0);">Б</span></font>олее ста первокурсников приняли участие в конкурсе компьютерных рисунков «Осенние мотивы», организованном кафедрой информатики СЛИ.', 'Более ста первокурсников приняли участие в конкурсе компьютерных рисунков «Осенние мотивы», организованном кафедрой информатики СЛИ.', 1, '2008-01-17 23:23:05'),
(4, 5, 'Проект БЕГИН', '&nbsp;&nbsp;&nbsp; <font size="7"><span style="color: rgb(255, 0, 0);">Р</span></font>уководитель центра ГИС-технологий СЛИ В.С. Акишин выступит с докладом «Проект БЕГИН – развитие высшего образования в области ГИС на севере России» на международной XIII Конференции пользователей ESRI и LeicaGeosystems в России и странах СНГ.', 'Руководитель центра ГИС-технологий СЛИ В.С. Акишин выступит с докладом «Проект БЕГИН – развитие высшего образования в области ГИС на севере России» на международной XIII Конференции пользователей ESRI и LeicaGeosystems в России и странах СНГ.', 1, '2008-01-17 23:23:57'),
(5, 2, 'Вышли из печати две новые монографии директора...', '&nbsp;&nbsp;&nbsp; <font style="color: rgb(255, 0, 0);" size="7">В</font>ышли из печати две новые монографии директора СЛИ профессора Н.М. Большакова.', '    Вышли из печати две новые монографии директора СЛИ профессора Н.М. Большакова.', 1, '2008-01-17 23:23:46'),
(6, 7, 'XIII съезд Русского энтомологического общества', '&nbsp;&nbsp;&nbsp; <font style="color: rgb(255, 0, 0);" size="7">З</font>аведующая кафедрой воспроизводства лесных ресурсов д.б.н., профессор Е.В. Юркина об участии в XIII съезде Русского энтомологического общества.', 'Заведующая кафедрой воспроизводства лесных ресурсов д.б.н., профессор Е.В. Юркина об участии в XIII съезде Русского энтомологического общества.', 1, '2008-01-17 23:24:34'),
(7, 1, 'IX Международный Лесной Форум – 2007', '&nbsp;&nbsp;&nbsp; <font style="color: rgb(255, 0, 0);" size="7">В</font>новь назначенный директор СЛИ профессор В.В. Жиделева приняла участие в одном из самых знаковых событий в лесопромышленном комплексе России – в IX Международном Лесном Форуме – 2007.', 'Вновь назначенный директор СЛИ профессор В.В. Жиделева приняла участие в одном из самых знаковых событий в лесопромышленном комплексе России – в IX Международном Лесном Форуме – 2007.', 1, '2008-01-17 23:26:08'),
(8, 8, 'Клуб программистов СЛИ', '<font size="7"><span style="color: rgb(255, 0, 0);">У</span></font>важаемые студенты! Приглашаем вступить в клуб программистов СЛИ, улучшить свои знания и стать профессиональным программистом, поделиться своим опытом с другими.', 'Уважаемые студенты! Приглашаем вступить в клуб программистов СЛИ, улучшить свои знания и стать профессиональным программистом, поделиться своим опытом с другими.', 1, '2008-01-17 23:28:27'),
(9, 6, 'Хостер сайта http://sli.komi.com/ опять в дауне', 'The requested URL could not be retrieved<br><br>While trying to retrieve the URL: http://sli.komi.com/ <br><br>The following error was encountered: <br>Unable to determine IP address from host name for sli.komi.com <br><br>The dnsserver returned: <br>No DNS records <br><br>This means that: <br> The cache was not able to resolve the hostname presented in the URL. <br> Check if the address is correct. <br>', 'The requested URL could not be retrieved\r\n...', 1, '2008-01-17 23:30:50');

INSERT INTO `department` (`key_department`, `title`, `id_faculty`) VALUES
(2, 'Информационные системы', 2),
(3, 'Автомобили и автомобильное хозяйство', 3),
(4, 'Дорожное, промышленное и гражданское строительство', 3),
(5, 'Инженерной графики и автоматизации проектирования', 3),
(6, 'Общетехнических дисциплин', 3),
(7, 'Машины и оборудование лесного комплекса', 3),
(8, 'Технической механики', 3),
(9, 'Технологии деревообрабатывающих производств', 3),
(10, 'Теплотехники и гидравлики', 2),
(11, 'Высшей математики', 2),
(12, 'Физики', 2),
(13, 'Химии', 2),
(14, 'Информатики', 2),
(16, 'Целлюлозно-бумажного производства, лесохимии и промышленной экологии', 2),
(17, 'Общей и прикладной экологии', 2),
(18, 'Лесного хозяйства', 4),
(19, 'Воспроизводства лесных ресурсов', 4),
(20, 'Гуманитарных и социальных дисциплин', 4),
(21, 'Электрификации и механизации сельского хозяйства', 4),
(22, 'Бухгалтерского учета, анализа, аудита и налогообложения', 5),
(23, 'Менеджмента и маркетинга', 5),
(24, 'Экономической теории и прикладной экономики', 5),
(25, 'Иностранных языков', 5),
(26, 'Экономика отраслевых производств', 5),
(27, 'Физическая культура и спорт', 5);

INSERT INTO `faculty` (`key_faculty`, `title`) VALUES
(2, 'Технологический'),
(3, 'Лесотранспортный'),
(4, 'Сельскохозяйственный'),
(5, 'Эконономики и управления');

INSERT INTO `groups` (`key_groups`, `title`, `id_specialization`, `year`) VALUES
(1, '355', 5, 5),
(2, '356', 5, 5);

INSERT INTO `linkdepartment` (`key_linkdepartment`, `id_users`, `id_department`) VALUES
(4, 1, 2);

INSERT INTO `linkgroups` (`key_linkgroups`, `id_users`, `id_groups`) VALUES
(18, 4, 1),
(17, 1, 1);

INSERT INTO `section` (`key_section`, `title`, `parent`, `level`) VALUES
(1, 'Новости', 0, 0),
(2, 'Статьи', 0, 0),
(5, 'О вузе', 1, 1),
(6, 'О сайте', 1, 1),
(7, 'Преподавателю', 2, 1),
(8, 'Студенту', 2, 1);

INSERT INTO `specialization` (`key_specialization`, `title`, `id_department`) VALUES
(5, 'Информационые системы', 2),
(6, 'Автомобили и автомобильное хозяйство', 3),
(7, 'Машины и оборудование лесного комплекса', 3),
(8, 'Технология химической переработки древесины', 16),
(9, 'Охрана окружающей среды и рациональное использование природных ресурсов', 17),
(10, 'Бухгалтерский учет, анализ и аудит', 22),
(11, 'Менеджмент организации', 23),
(12, 'Экономика и управление на предприятии (лесное хозяйство и лесная промышленность)', 24),
(13, 'Экономика и управление на предприятии (аграрно-промышленный комплекс)', 26),
(14, 'Менеджмент организации', 23),
(15, 'Механизация сельского хозяйства', 21),
(16, 'Электрификация и автоматизация сельского хозяйства', 21),
(17, 'Лесное хозяйство', 18);

INSERT INTO `themes` (`key_themes`, `title`) VALUES
(2, 'pink'),
(3, 'blue'),
(4, 'grey'),
(5, 'purplehaze');

INSERT INTO `users` (`key_users`, `login`, `password`, `hash`, `image`, `email`, `id_userprivs`, `id_themes`, `name`, `surname`, `patronymic`, `sex`, `birthdate`, `regdate`, `status`, `note`) VALUES
(2, '123', 'd9b1d7db4cd6e70935368a1efb10e377', '37f4e15095f4d3e2e120bb4d5e1e5f31', '123.jpg', '', 2, 1, '', '', '', 'Не указано', '1985-02-07', '2008-01-16 23:36:33', 'Заполни ФИО', ''),
(3, 'edit', '3e5971096b368fa83726d67805a3ee3d', '8505674efdd1cb24259e9db25e59a39c', 'edit.jpg', '', 4, 1, '', '', '', '', '1900-01-01', '2008-01-17 21:10:36', 'Главред', ''),
(4, 'student', 'abd842186026b1110b67d3e39a96e809', '07a90c056d5595d1b9a4c4842e2dc017', 'student.jpg', '1@1.ru', 2, 1, 'Студент', 'Студентов', 'Студентович', 'Мужской', '1900-01-01', '2008-01-18 21:50:04', '', '');