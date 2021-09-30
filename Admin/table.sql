CREATE TABLE IF NOT EXISTS `Test_neuro_compte_Admin` (
  `id` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `mdp` varchar(32) DEFAULT NULL,
  `activate` int(1) NOT NULL DEFAULT '0',
  `type` varchar(5) NOT NULL DEFAULT 'user',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `Test_neuro_Admin_secu_mdp` (
  `id` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `hash` varchar(32) NOT NULL,
  `email` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `Test_neuro_Album` (
  `id` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `lien` longtext,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `Test_neuro_Article` (
  `id` int(32) unsigned NOT NULL AUTO_INCREMENT,
  `position` int(5) NOT NULL DEFAULT '1',
  `message` longtext NOT NULL,
  `page` longtext NOT NULL,
  `statue` int(1) NOT NULL DEFAULT '1',
  `created` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `Test_neuro_Mail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `destinataire` longtext NOT NULL,
  `objet` longtext NOT NULL,
  `message` longtext NOT NULL,
  `retour` varchar(50) NOT NULL,
  `created` int(15) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `Test_neuro_Menu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Nom` varchar(50) NOT NULL,
  `page` longtext NOT NULL,
  `position` int(5) NOT NULL,
  `parent` int(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `Test_neuro_Page` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `titre` varchar(70) NOT NULL,
  `description`  varchar(170) NOT NULL,
  `category` varchar(30) NOT NULL,
  `page` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;