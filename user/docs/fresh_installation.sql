-- phpMyAdmin SQL Dump
-- version 3.3.8deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Mar 28, 2011 at 04:35 PM
-- Server version: 5.1.49
-- PHP Version: 5.3.3-1ubuntu9.1

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `testdrive`
--

-- --------------------------------------------------------

--
-- Table structure for table `action`
--

DROP TABLE IF EXISTS `action`;
CREATE TABLE IF NOT EXISTS `action` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `comment` text,
  `subject` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `action`
--

INSERT INTO `action` (`id`, `title`, `comment`, `subject`) VALUES
(1, 'message_write', NULL, NULL),
(2, 'message_receive', NULL, NULL),
(3, 'view_profile_visits', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `friendship`
--

DROP TABLE IF EXISTS `friendship`;
CREATE TABLE IF NOT EXISTS `friendship` (
  `inviter_id` int(11) NOT NULL,
  `friend_id` int(11) NOT NULL,
  `status` int(11) NOT NULL,
  `acknowledgetime` int(11) DEFAULT NULL,
  `requesttime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  `message` varchar(255) NOT NULL,
  PRIMARY KEY (`inviter_id`,`friend_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `friendship`
--


-- --------------------------------------------------------

--
-- Table structure for table `membership`
--

DROP TABLE IF EXISTS `membership`;
CREATE TABLE IF NOT EXISTS `membership` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `membership_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `payment_id` int(11) NOT NULL,
  `order_date` int(11) NOT NULL,
  `end_date` int(11) DEFAULT NULL,
  `payment_date` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10000 ;

--
-- Dumping data for table `membership`
--


-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

DROP TABLE IF EXISTS `messages`;
CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `timestamp` int(10) unsigned NOT NULL,
  `from_user_id` int(10) unsigned NOT NULL,
  `to_user_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text,
  `message_read` tinyint(1) NOT NULL,
  `answered` tinyint(1) NOT NULL,
  `draft` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `messages`
--


-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

DROP TABLE IF EXISTS `payment`;
CREATE TABLE IF NOT EXISTS `payment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `text` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id`, `title`, `text`) VALUES
(1, 'Prepayment', NULL),
(2, 'Paypal', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `permission`
--

DROP TABLE IF EXISTS `permission`;
CREATE TABLE IF NOT EXISTS `permission` (
  `principal_id` int(11) NOT NULL,
  `subordinate_id` int(11) NOT NULL DEFAULT '0',
  `type` enum('user','role') NOT NULL,
  `action` int(11) NOT NULL,
  `template` tinyint(1) NOT NULL,
  `comment` text,
  PRIMARY KEY (`principal_id`,`subordinate_id`,`type`,`action`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `permission`
--

INSERT INTO `permission` (`principal_id`, `subordinate_id`, `type`, `action`, `template`, `comment`) VALUES
(3, 0, 'role', 1, 0, 'Users can write messagse'),
(3, 0, 'role', 2, 0, 'Users can receive messagse'),
(3, 0, 'role', 3, 0, 'Users are able to view visits of his profile');

-- --------------------------------------------------------

--
-- Table structure for table `privacysetting`
--

DROP TABLE IF EXISTS `privacysetting`;
CREATE TABLE IF NOT EXISTS `privacysetting` (
  `user_id` int(10) unsigned NOT NULL,
  `message_new_friendship` tinyint(1) NOT NULL DEFAULT '1',
  `message_new_message` tinyint(1) NOT NULL DEFAULT '1',
  `message_new_profilecomment` tinyint(1) NOT NULL DEFAULT '1',
  `appear_in_search` tinyint(1) NOT NULL DEFAULT '1',
  `show_online_status` tinyint(1) NOT NULL DEFAULT '1',
  `log_profile_visits` tinyint(1) NOT NULL DEFAULT '1',
  `ignore_users` varchar(255) DEFAULT NULL,
  `public_profile_fields` bigint(15) unsigned DEFAULT NULL,
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `privacysetting`
--

INSERT INTO `privacysetting` (`user_id`, `message_new_friendship`, `message_new_message`, `message_new_profilecomment`, `appear_in_search`, `show_online_status`, `log_profile_visits`, `ignore_users`, `public_profile_fields`) VALUES
(2, 1, 1, 1, 1, 1, 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `profiles`
--

DROP TABLE IF EXISTS `profiles`;
CREATE TABLE IF NOT EXISTS `profiles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `privacy` enum('protected','private','public') NOT NULL,
  `lastname` varchar(50) NOT NULL DEFAULT '',
  `firstname` varchar(50) NOT NULL DEFAULT '',
  `show_friends` tinyint(1) DEFAULT '1',
  `allow_comments` tinyint(1) DEFAULT '1',
  `email` varchar(255) NOT NULL DEFAULT '',
  `street` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `about` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `profiles`
--

INSERT INTO `profiles` (`id`, `user_id`, `timestamp`, `privacy`, `lastname`, `firstname`, `show_friends`, `allow_comments`, `email`, `street`, `city`, `about`) VALUES
(1, 1, '2011-03-28 16:33:21', 'protected', 'admin', 'admin', 1, 1, 'webmaster@example.com', NULL, NULL, NULL),
(2, 2, '2011-03-28 16:33:21', 'protected', 'demo', 'demo', 1, 1, 'demo@example.com', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `profile_comment`
--

DROP TABLE IF EXISTS `profile_comment`;
CREATE TABLE IF NOT EXISTS `profile_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `createtime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `profile_comment`
--


-- --------------------------------------------------------

--
-- Table structure for table `profile_fields`
--

DROP TABLE IF EXISTS `profile_fields`;
CREATE TABLE IF NOT EXISTS `profile_fields` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `field_group_id` int(10) unsigned NOT NULL DEFAULT '0',
  `varname` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `hint` text NOT NULL,
  `field_type` varchar(50) NOT NULL DEFAULT '',
  `field_size` int(3) NOT NULL DEFAULT '0',
  `field_size_min` int(3) NOT NULL DEFAULT '0',
  `required` int(1) NOT NULL DEFAULT '0',
  `match` varchar(255) NOT NULL DEFAULT '',
  `range` varchar(255) NOT NULL DEFAULT '',
  `error_message` varchar(255) NOT NULL DEFAULT '',
  `other_validator` varchar(255) NOT NULL DEFAULT '',
  `default` varchar(255) NOT NULL DEFAULT '',
  `position` int(3) NOT NULL DEFAULT '0',
  `visible` int(1) NOT NULL DEFAULT '0',
  `related_field_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `varname` (`varname`,`visible`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `profile_fields`
--

INSERT INTO `profile_fields` (`id`, `field_group_id`, `varname`, `title`, `hint`, `field_type`, `field_size`, `field_size_min`, `required`, `match`, `range`, `error_message`, `other_validator`, `default`, `position`, `visible`, `related_field_name`) VALUES
(1, 0, 'email', 'E-Mail', '', 'VARCHAR', 255, 0, 1, '', '', '', 'CEmailValidator', '', 0, 3, NULL),
(2, 0, 'firstname', 'First name', '', 'VARCHAR', 255, 0, 1, '', '', '', '', '', 0, 3, NULL),
(3, 0, 'lastname', 'Last name', '', 'VARCHAR', 255, 0, 1, '', '', '', '', '', 0, 3, NULL),
(4, 0, 'street', 'Street', '', 'VARCHAR', 255, 0, 0, '', '', '', '', '', 0, 3, NULL),
(5, 0, 'city', 'City', '', 'VARCHAR', 255, 0, 0, '', '', '', '', '', 0, 3, NULL),
(6, 0, 'about', 'About', '', 'TEXT', 255, 0, 0, '', '', '', '', '', 0, 3, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `profile_fields_group`
--

DROP TABLE IF EXISTS `profile_fields_group`;
CREATE TABLE IF NOT EXISTS `profile_fields_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `group_name` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `position` int(3) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `profile_fields_group`
--


-- --------------------------------------------------------

--
-- Table structure for table `profile_visit`
--

DROP TABLE IF EXISTS `profile_visit`;
CREATE TABLE IF NOT EXISTS `profile_visit` (
  `visitor_id` int(11) NOT NULL,
  `visited_id` int(11) NOT NULL,
  `timestamp_first_visit` int(11) NOT NULL,
  `timestamp_last_visit` int(11) NOT NULL,
  `num_of_visits` int(11) NOT NULL,
  PRIMARY KEY (`visitor_id`,`visited_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `profile_visit`
--


-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `selectable` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Selectable on Registration?',
  `searchable` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Can be searched',
  `autoassign` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Autoassign on new users',
  `is_membership_possible` tinyint(1) NOT NULL DEFAULT '0',
  `price` double DEFAULT NULL COMMENT 'Price (when using membership module)',
  `duration` int(11) DEFAULT NULL COMMENT 'How long a membership is valid',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `title`, `description`, `selectable`, `searchable`, `autoassign`, `is_membership_possible`, `price`, `duration`) VALUES
(1, 'UserCreator', 'This users can create new Users', 0, 0, 0, 0, 0, 0),
(2, 'UserRemover', 'This users can remove other Users', 0, 0, 0, 0, 0, 0),
(3, 'Demo', 'Users having the demo role', 0, 1, 0, 0, 0, 0),
(4, 'Business', 'Example Business account', 0, 0, 0, 0, 9.99, 7),
(5, 'Premium', 'Example Premium account', 0, 0, 0, 0, 19.99, 28);

-- --------------------------------------------------------

--
-- Table structure for table `usergroup`
--

DROP TABLE IF EXISTS `usergroup`;
CREATE TABLE IF NOT EXISTS `usergroup` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `owner_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `usergroup`
--


-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(20) NOT NULL,
  `password` varchar(128) NOT NULL,
  `activationKey` varchar(128) NOT NULL DEFAULT '',
  `createtime` int(10) NOT NULL DEFAULT '0',
  `lastvisit` int(10) NOT NULL DEFAULT '0',
  `lastaction` int(10) NOT NULL DEFAULT '0',
  `lastpasswordchange` int(10) NOT NULL DEFAULT '0',
  `superuser` int(1) NOT NULL DEFAULT '0',
  `status` int(1) NOT NULL DEFAULT '0',
  `avatar` varchar(255) DEFAULT NULL,
  `notifyType` enum('None','Digest','Instant','Treshhold') DEFAULT 'Instant',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `status` (`status`),
  KEY `superuser` (`superuser`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `activationKey`, `createtime`, `lastvisit`, `lastaction`, `lastpasswordchange`, `superuser`, `status`, `avatar`, `notifyType`) VALUES
(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', '', 1301322801, 0, 0, 0, 1, 1, NULL, 'Instant'),
(2, 'demo', 'fe01ce2a7fbac8fafaed7c982a04e229', '', 1301322801, 0, 0, 0, 0, 1, NULL, 'Instant');

-- --------------------------------------------------------

--
-- Table structure for table `user_has_role`
--

DROP TABLE IF EXISTS `user_has_role`;
CREATE TABLE IF NOT EXISTS `user_has_role` (
  `user_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_has_role`
--

INSERT INTO `user_has_role` (`user_id`, `role_id`) VALUES
(2, 3);

-- --------------------------------------------------------

--
-- Table structure for table `user_has_usergroup`
--

DROP TABLE IF EXISTS `user_has_usergroup`;
CREATE TABLE IF NOT EXISTS `user_has_usergroup` (
  `user_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  `jointime` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `user_has_usergroup`
--


-- --------------------------------------------------------

--
-- Table structure for table `yumsettings`
--

DROP TABLE IF EXISTS `yumsettings`;
CREATE TABLE IF NOT EXISTS `yumsettings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '0',
  `preserveProfiles` tinyint(1) NOT NULL DEFAULT '1',
  `enableAvatar` tinyint(1) NOT NULL DEFAULT '1',
  `registrationType` tinyint(1) NOT NULL DEFAULT '4',
  `enableRecovery` tinyint(1) NOT NULL DEFAULT '1',
  `enableProfileHistory` tinyint(1) NOT NULL DEFAULT '1',
  `messageSystem` enum('None','Plain','Dialog') NOT NULL DEFAULT 'Dialog',
  `notifyType` enum('None','Digest','Instant','User','Treshhold') NOT NULL DEFAULT 'User',
  `password_expiration_time` int(11) DEFAULT NULL,
  `readOnlyProfiles` tinyint(1) NOT NULL DEFAULT '0',
  `loginType` int(11) NOT NULL,
  `notifyemailchange` enum('oldemail','newemail') DEFAULT NULL,
  `enableCaptcha` tinyint(1) NOT NULL DEFAULT '1',
  `ldap_host` varchar(255) DEFAULT NULL,
  `ldap_port` int(5) DEFAULT NULL,
  `ldap_basedn` varchar(255) DEFAULT NULL,
  `ldap_protocol` enum('2','3') NOT NULL DEFAULT '3',
  `ldap_autocreate` tinyint(1) NOT NULL DEFAULT '1',
  `ldap_tls` tinyint(1) NOT NULL DEFAULT '0',
  `ldap_transfer_attr` tinyint(1) NOT NULL DEFAULT '1',
  `ldap_transfer_pw` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `yumsettings`
--

INSERT INTO `yumsettings` (`id`, `title`, `is_active`, `preserveProfiles`, `enableAvatar`, `registrationType`, `enableRecovery`, `enableProfileHistory`, `messageSystem`, `notifyType`, `password_expiration_time`, `readOnlyProfiles`, `loginType`, `notifyemailchange`, `enableCaptcha`, `ldap_host`, `ldap_port`, `ldap_basedn`, `ldap_protocol`, `ldap_autocreate`, `ldap_tls`, `ldap_transfer_attr`, `ldap_transfer_pw`) VALUES
(1, 'Yum factory Default', 1, 1, 1, 4, 1, 1, 'Dialog', 'Instant', 30, 0, 3, NULL, 1, NULL, NULL, NULL, '3', 1, 0, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `yumtextsettings`
--

DROP TABLE IF EXISTS `yumtextsettings`;
CREATE TABLE IF NOT EXISTS `yumtextsettings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `language` enum('en_us','de','fr','pl','ru','es') NOT NULL DEFAULT 'en_us',
  `text_registration_header` text,
  `text_registration_footer` text,
  `text_login_header` text,
  `text_login_footer` text,
  `text_email_registration` text,
  `subject_email_registration` text,
  `text_email_recovery` text,
  `text_email_activation` text,
  `text_friendship_new` text,
  `text_friendship_confirmed` text,
  `text_profilecomment_new` text,
  `text_message_new` text,
  `text_membership_ordered` text,
  `text_payment_arrived` text,
  `text_membership_header` text,
  `text_membership_footer` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `yumtextsettings`
--

INSERT INTO `yumtextsettings` (`id`, `language`, `text_registration_header`, `text_registration_footer`, `text_login_header`, `text_login_footer`, `text_email_registration`, `subject_email_registration`, `text_email_recovery`, `text_email_activation`, `text_friendship_new`, `text_friendship_confirmed`, `text_profilecomment_new`, `text_message_new`, `text_membership_ordered`, `text_payment_arrived`, `text_membership_header`, `text_membership_footer`) VALUES
(1, 'en_us', 'Welcome at the registration System', 'When registering at this System, you automatically accept our terms.', 'Welcome!', '', 'You have registered for this Application. To confirm your E-Mail address, please visit {activation_url}', 'You have registered for an application', 'You have requested a new Password. To set your new Password,\n										please go to {activation_url}', 'Your account has been activated. Thank you for your registration.', 'New friendship Request from {username}: {message}. To accept or ignore this request, go to your friendship page: {link_friends} or go to your profile: {link_profile}', 'The User {username} has accepted your friendship request', 'You have a new profile comment from {username}: {message} visit your profile: {link_profile}', 'You have received a new message from {username}: {message}', 'Your order of membership {membership} on {order_date} has been taken. Your order Number is {id}. You have choosen the payment style {payment}.', 'Your payment has been received on {payment_date} and your Membership {id} is now active', 'Please select a Membership of your choice', 'Your advantages: <br /> Premium: Everything is better <br /> Business: Everything is much better '),
(2, 'de', 'Willkommen zum System.', 'Mit der Anmeldung bestätigen Sie unsere allgemeinen Bedingungen.', 'Willkommen!', '', 'Sie haben sich für unsere Applikation registriert. Bitte bestätigen Sie ihre E-Mail adresse mit diesem Link: {activation_url}', 'Sie haben sich für eine Applikation registriert.', 'Sie haben ein neues Passwort angefordert. Bitte klicken Sie diesen link: {activation_url}', 'Ihr Konto wurde freigeschaltet.', 'Der Benutzer {username} hat Ihnen eine Freundschaftsanfrage gesendet. \n\n							 Nachricht: {message}\n\n							 Klicken sie <a href="{link_friends}">hier</a>, um diese Anfrage zu bestätigen oder zu ignorieren. Alternativ können sie <a href="{link_profile}">hier</a> auf ihre Profilseite zugreifen.', 'Der Benutzer {username} hat ihre Freundschaftsanfrage bestätigt.', '\n							 Benutzer {username} hat Ihnen eine Nachricht auf Ihrer Pinnwand hinterlassen: \n\n							 {message}\n\n							 <a href="{link}">hier</a> geht es direkt zu Ihrer Pinnwand!', 'Sie haben eine neue Nachricht von {username} bekommen: {message}', 'Ihre Bestellung der Mitgliedschaft {membership} wurde am {order_date} entgegen genommen. Die gewählte Zahlungsart ist {payment}. Die Auftragsnummer lautet {id}.', 'Ihre Zahlung wurde am {payment_date} entgegen genommen. Ihre Mitgliedschaft mit der Nummer {id} ist nun Aktiv.', 'Bitte wählen Sie ein Paket ihrer Wahl:', 'Ihre Vorteile: '),
(3, 'es', 'Bienvenido al sistema de registro', 'Al registrarse en este sistema, usted está aceptando nuestros términos.', '¡Bienvenido!', '', 'Te has registrado en esta aplicación. Para confirmar tu dirección de correo electrónico, por favor, visita {activation_url}.', 'Te has registrado en esta aplicación.', 'Has solicitado una nueva contraseña. Para establecer una nueva contraseña, por favor ve a {activation_url}', 'Tu cuenta ha sido activada. Gracias por registrarte.', 'Has recibido una nueva solicitud de amistad de {user_from}: {message} Ve a tus contactos: {link}', 'Tienes un nuevo comentario en tu perfil de {username}: {message} visita tu perfil: {link}', 'Please translatore thisse hiere toh tha espagnola langsch {username}', 'Has recibido un mensaje de {username}: {message}', 'Tu orden de membresía {membership} de fecha {order_date} fué tomada. Tu número de orden es {id}. Escogiste como modo de pago {payment}.', 'Tu pago fué recibido en fecha {payment_date}. Ahora tu Membresía {id} ya está activa', 'Por favor, selecciona un tipo de Membresía', 'Las ventajas: <br /> Premium: Todo es mejor <br /> Business: Todo es aún mucho mejor ');
SET FOREIGN_KEY_CHECKS=1;
