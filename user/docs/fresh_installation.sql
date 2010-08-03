SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `from_user_id` int(10) unsigned NOT NULL,
  `to_user_id` int(10) unsigned NOT NULL,
  `title` varchar(45) NOT NULL,
  `message` text,
  `message_read` tinyint(1) NOT NULL,
  `draft` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_messages_users` (`from_user_id`),
  KEY `fk_messages_users1` (`to_user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `profiles` (
  `profile_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `privacy` enum('protected','private','public') NOT NULL,
  `lastname` varchar(50) NOT NULL DEFAULT '',
  `firstname` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `street` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `about` text,
  PRIMARY KEY (`profile_id`),
  KEY `fk_profiles_users` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `profiles` (`profile_id`, `user_id`, `timestamp`, `privacy`, `lastname`, `firstname`, `email`, `street`, `city`, `about`) VALUES
(1, 1, '2010-08-03 21:07:17', 'protected', 'admin', 'admin', 'webmaster@example.com', NULL, NULL, NULL),
(2, 2, '2010-08-03 21:07:17', 'protected', 'demo', 'demo', 'demo@example.com', NULL, NULL, NULL);

CREATE TABLE IF NOT EXISTS `profile_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `field_group_id` int(10) unsigned NOT NULL DEFAULT '0',
  `varname` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `hint` text NOT NULL,
  `field_type` varchar(50) NOT NULL,
  `field_size` int(3) NOT NULL DEFAULT '0',
  `field_size_min` int(3) NOT NULL DEFAULT '0',
  `required` int(1) NOT NULL DEFAULT '0',
  `match` varchar(255) NOT NULL,
  `range` varchar(255) NOT NULL,
  `error_message` varchar(255) NOT NULL,
  `other_validator` varchar(255) NOT NULL,
  `default` varchar(255) NOT NULL,
  `position` int(3) NOT NULL DEFAULT '0',
  `visible` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `varname` (`varname`,`visible`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

INSERT INTO `profile_fields` (`id`, `field_group_id`, `varname`, `title`, `hint`, `field_type`, `field_size`, `field_size_min`, `required`, `match`, `range`, `error_message`, `other_validator`, `default`, `position`, `visible`) VALUES
(1, 0, 'email', 'E-Mail', '', 'VARCHAR', 255, 0, 1, '', '', '', 'CEmailValidator', '', 0, 2),
(2, 0, 'firstname', 'First name', '', 'VARCHAR', 255, 0, 1, '', '', '', '', '', 0, 2),
(3, 0, 'lastname', 'Last name', '', 'VARCHAR', 255, 0, 1, '', '', '', '', '', 0, 2),
(4, 0, 'street', 'Street', '', 'VARCHAR', 255, 0, 0, '', '', '', '', '', 0, 1),
(5, 0, 'city', 'City', '', 'VARCHAR', 255, 0, 0, '', '', '', '', '', 0, 1),
(6, 0, 'about', 'About', '', 'TEXT', 255, 0, 0, '', '', '', '', '', 0, 1);

CREATE TABLE IF NOT EXISTS `profile_fields_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `position` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `roles` (`id`, `title`, `description`) VALUES
(1, 'UserCreator', 'This users can create new Users'),
(2, 'UserRemover', 'This users can remove other Users');

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(128) NOT NULL,
  `activationKey` varchar(128) NOT NULL DEFAULT '',
  `createtime` int(10) NOT NULL DEFAULT '0',
  `lastvisit` int(10) NOT NULL DEFAULT '0',
  `superuser` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `status` (`status`),
  KEY `superuser` (`superuser`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `users` (`id`, `username`, `password`, `activationKey`, `createtime`, `lastvisit`, `superuser`, `status`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '', 1280862436, 0, 1, 1),
(2, 'demo', 'fe01ce2a7fbac8fafaed7c982a04e229', '', 1280862436, 0, 0, 1);

CREATE TABLE IF NOT EXISTS `user_has_role` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `user_has_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) unsigned NOT NULL,
  `child_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


