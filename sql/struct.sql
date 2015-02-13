
;
DROP TABLE IF EXISTS `themes`;
CREATE TABLE IF NOT EXISTS `themes` (
  `key_themes` bigint(10) NOT NULL auto_increment,
  `title` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`key_themes`),
  UNIQUE KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `userprivs`;
CREATE TABLE IF NOT EXISTS `userprivs` (
  `key_userprivs` bigint(10) NOT NULL auto_increment,
  `title` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`key_userprivs`),
  UNIQUE KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `key_users` bigint(10) NOT NULL auto_increment,
  `login` varchar(10) NOT NULL default '',
  `password` varchar(255) NOT NULL default '',
  `hash` varchar(255) NOT NULL default '',
  `image` varchar(255) NOT NULL default '',
  `email` varchar(50) NOT NULL default '',
  `id_userprivs` tinyint(1) NOT NULL default '1',
  `id_themes` bigint(10) NOT NULL default '0',
  `name` varchar(100) NOT NULL default '',
  `surname` varchar(100) NOT NULL default '',
  `patronymic` varchar(100) NOT NULL default '',
  `sex` enum('Не указано','Мужской','Женский') NOT NULL default 'Не указано',
  `birthdate` date NOT NULL default '0000-00-00',
  `regdate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `status` varchar(255) NOT NULL default '',
  `note` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`key_users`),
  UNIQUE KEY `login` (`login`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `themes` (`key_themes`, `title`) VALUES
(1, 'default');

INSERT INTO `userprivs` (`key_userprivs`, `title`) VALUES
(1, 'Неизвестно'),
(2, 'Студент'),
(3, 'Преподаватель'),
(4, 'Редактор'),
(5, 'Модератор'),
(6, 'Администратор');
