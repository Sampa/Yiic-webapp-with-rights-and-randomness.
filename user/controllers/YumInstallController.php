<?php

class YumInstallController extends YumController {
	public $layout = 'install';
	public $defaultAction='install';

	public function accessRules() {
		return array(
				array('allow',
					'actions'=>array(
						'index, start, installer, installation, install, index'),
					'users'=>array('@')),
				);
	}

	public function actionStart() {
		$this->actionInstall();
	}

	public function actionInstaller() {
		$this->actionInstall();
	}

	public function actionInstallation() {
		$this->actionInstall();
	}

	public function actionInstall() {
		if($this->module->debug === true) {
			if(Yii::app()->request->isPostRequest) {
				if($db = Yii::app()->db) {
					$sql = 'set FOREIGN_KEY_CHECKS = 0;';
					$db->createCommand($sql)->execute();

					$transaction = $db->beginTransaction();

					$tables = array(
							'usersTable',
							'privacySettingTable',
							'profileFieldsTable',
							'profileTable',
							'profileCommentTable',
							'profileVisitTable',
							'membershipTable',
							'paymentTable',
							'messagesTable',
							'rolesTable',
							'userRoleTable',
							'permissionTable',
							'friendshipTable',
							'actionTable',
							'usergroupTable',
							'usergroupMessagesTable',
							'textSettingsTable');

					foreach($tables as $table) {
						if(isset($_POST[$table])) {
							${$table} = $_POST[$table];

							// Clean up existing Installation table-by-table
							$db->createCommand(sprintf('drop table if exists %s',
										${$table}))->execute();
						}
					}


					// Create User Table
					$sql = "CREATE TABLE IF NOT EXISTS `" . $usersTable . "` (
						`id` int unsigned NOT NULL auto_increment,
						`username` varchar(20) NOT NULL,
						`password` varchar(128) NOT NULL,
						`activationKey` varchar(128) NOT NULL default '',
						`createtime` int(10) NOT NULL default '0',
						`lastvisit` int(10) NOT NULL default '0',
						`lastaction` int(10) NOT NULL default '0',
						`lastpasswordchange` int(10) NOT NULL default '0',
						`superuser` int(1) NOT NULL default '0',
						`status` int(1) NOT NULL default '0',
						`avatar` varchar(255) default NULL,
						`notifyType` enum('None', 'Digest', 'Instant', 'Treshhold') default 'Instant',
						PRIMARY KEY  (`id`),
						UNIQUE KEY `username` (`username`),
						KEY `status` (`status`),
						KEY `superuser` (`superuser`)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
					$db->createCommand($sql)->execute();

					$sql = "CREATE TABLE IF NOT EXISTS `". $privacySettingTable . "` (
						`user_id` int unsigned NOT NULL,
						`message_new_friendship` tinyint(1) NOT NULL DEFAULT 1,
						`message_new_message` tinyint(1) NOT NULL DEFAULT 1,
						`message_new_profilecomment` tinyint(1) NOT NULL DEFAULT 1,
						`appear_in_search` tinyint(1) NOT NULL DEFAULT 1,
						`show_online_status` tinyint(1) NOT NULL DEFAULT 1,
						`log_profile_visits` tinyint(1) NOT NULL DEFAULT 1,
						`ignore_users` varchar(255),
						`public_profile_fields` bigint(15) unsigned,
						PRIMARY KEY (`user_id`)
							) ENGINE=InnoDB DEFAULT CHARSET=utf8;
					";
					$db->createCommand($sql)->execute();

					if(isset($_POST['installUsergroup'])) {  
						$sql = "CREATE TABLE IF NOT EXISTS `".$usergroupTable."` (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`owner_id` int(11) NOT NULL,
							`participants` text NULL,
							`title` varchar(255) NOT NULL,
							`description` text NOT NULL,
							PRIMARY KEY (`id`)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

						$db->createCommand($sql)->execute();

						$sql = "CREATE TABLE IF NOT EXISTS `".$usergroupMessagesTable."` (
							`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
							`author_id` int(11) unsigned NOT NULL,
							`group_id` int(11) unsigned NOT NULL,
							`createtime` int(11) unsigned NOT NULL,
							`title` varchar(255) NOT NULL,
							`message` text NOT NULL,
							PRIMARY KEY (`id`)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

						$db->createCommand($sql)->execute();
					}

					if(isset($_POST['installMembership'])) {  
						$sql = "CREATE TABLE IF NOT EXISTS `{$membershipTable}` (
							`id` int(11) NOT NULL PRIMARY KEY AUTO_INCREMENT,
							`membership_id` int(11) NOT NULL,
							`user_id` int(11) NOT NULL,
							`payment_id` int(11) NOT NULL,
							`order_date` int(11) NOT NULL,
							`end_date` int(11) DEFAULT NULL,
							`payment_date` int(11) NULL
								) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=10000;";

						$db->createCommand($sql)->execute();

						$sql = " CREATE TABLE IF NOT EXISTS `{$paymentTable}` (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`title` varchar(255) NOT NULL,
							`text` text,
							PRIMARY KEY (`id`)
								) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ";
						$db->createCommand($sql)->execute();
					}
					if(isset($_POST['installPermission'])) {  
						$sql = "CREATE TABLE IF NOT EXISTS `".$actionTable."` (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`title` varchar(255) NOT NULL,
							`comment` text,
							`subject` varchar(255) DEFAULT NULL,
							PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ";

						$db->createCommand($sql)->execute();
						$sql = "CREATE TABLE IF NOT EXISTS `".$permissionTable."` (
							`principal_id` int(11) NOT NULL,
							`subordinate_id` int(11) NULL,
							`type` enum('user','role') NOT NULL,
							`action` int(11) NOT NULL,
							`template` tinyint(1) NOT NULL,
							`comment` text,
							PRIMARY KEY (`principal_id`,`subordinate_id`,`type`,`action`)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8; ";

						$db->createCommand($sql)->execute();
					}
					if(isset($_POST['installSettingsTable'])) {  
						// Create Text settings table
						$sql = "CREATE TABLE IF NOT EXISTS `" . $textSettingsTable . "` (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`language` enum('en_us','de','fr','pl','ru','es','ro') NOT NULL DEFAULT 'en_us',
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
							PRIMARY KEY (`id`)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
						$db->createCommand($sql)->execute();

						$sql = "
							INSERT INTO `".$textSettingsTable."` (`id`,
									`language`,
									`text_email_registration`,
									`subject_email_registration`,
									`text_email_recovery`,
									`text_email_activation`,
									`text_friendship_new`,
									`text_friendship_confirmed`,
									`text_profilecomment_new`,
									`text_message_new`,
									`text_membership_ordered`,
									`text_payment_arrived`
									) VALUES ('1',
										'en_us',
										'You have registered for this Application. To confirm your E-Mail address, please visit {activation_url}',
										'You have registered for an application',
										'You have requested a new Password. To set your new Password,
										please go to {activation_url}',
										'Your account has been activated. Thank you for your registration.',
										'New friendship Request from {username}: {message}. To accept or ignore this request, go to your friendship page: {link_friends} or go to your profile: {link_profile}',
										'The User {username} has accepted your friendship request',
										'You have a new profile comment from {username}: {message} visit your profile: {link_profile}',
										'You have received a new message from {username}: {message}',
'Your order of membership {membership} on {order_date} has been taken. Your order Number is {id}. You have choosen the payment style {payment}.',
'Your payment has been received on {payment_date} and your Membership {id} is now active'
),
							('2',
							 'de',
							 'Sie haben sich für unsere Applikation registriert. Bitte bestätigen Sie ihre E-Mail adresse mit diesem Link: {activation_url}',
							 'Sie haben sich für eine Applikation registriert.',
							 'Sie haben ein neues Passwort angefordert. Bitte klicken Sie diesen link: {activation_url}',
							 'Ihr Konto wurde freigeschaltet.',
							 'Der Benutzer {username} hat Ihnen eine Freundschaftsanfrage gesendet. 

							 Nachricht: {message}

							 Klicken sie <a href=\"{link_friends}\">hier</a>, um diese Anfrage zu bestätigen oder zu ignorieren. Alternativ können sie <a href=\"{link_profile}\">hier</a> auf ihre Profilseite zugreifen.',
							'Der Benutzer {username} hat ihre Freundschaftsanfrage bestätigt.',
							 '
							 Benutzer {username} hat Ihnen eine Nachricht auf Ihrer Pinnwand hinterlassen: 

							 {message}

							 <a href=\"{link}\">hier</a> geht es direkt zu Ihrer Pinnwand!',
								 'Sie haben eine neue Nachricht von {username} bekommen: {message}',
'Ihre Bestellung der Mitgliedschaft {membership} wurde am {order_date} entgegen genommen. Die gewählte Zahlungsart ist {payment}. Die Auftragsnummer lautet {id}.',
'Ihre Zahlung wurde am {payment_date} entgegen genommen. Ihre Mitgliedschaft mit der Nummer {id} ist nun Aktiv.'),
								 ('3',
									'es',
									'Te has registrado en esta aplicación. Para confirmar tu dirección de correo electrónico, por favor, visita {activation_url}.',
									'Te has registrado en esta aplicación.',
									'Has solicitado una nueva contraseña. Para establecer una nueva contraseña, por favor ve a {activation_url}',
									'Tu cuenta ha sido activada. Gracias por registrarte.',
									'Has recibido una nueva solicitud de amistad de {user_from}: {message} Ve a tus contactos: {link}',
									'Tienes un nuevo comentario en tu perfil de {username}: {message} visita tu perfil: {link}',
									'Please translatore thisse hiere toh tha espagnola langsch {username}',
									'Has recibido un mensaje de {username}: {message}',
									'Tu orden de membresía {membership} de fecha {order_date} fué tomada. Tu número de orden es {id}. Escogiste como modo de pago {payment}.',
									'Tu pago fué recibido en fecha {payment_date}. Ahora tu Membresía {id} ya está activa'), 
('4', 'fr', '', '', '', '', '', '', '', '', '', ''), ('5', 'ro', '', '', '', '', '', '', '', '', '', '') ; ";

						$db->createCommand($sql)->execute();
					}

					if(isset($_POST['installFriendship']))  
					{
						$sql = "CREATE TABLE  `".$friendshipTable."` (
							`inviter_id` int(11) NOT NULL,
							`friend_id` int(11) NOT NULL,
							`status` int(11) NOT NULL,
							`acknowledgetime` int(11) DEFAULT NULL,
							`requesttime` int(11) DEFAULT NULL,
							`updatetime` int(11) DEFAULT NULL,
							`message` varchar(255) NOT NULL,
							PRIMARY KEY (`inviter_id`, `friend_id`)
								) ENGINE = INNODB;";

						$db->createCommand($sql)->execute();
					}

					if(isset($_POST['installProfiles']))  
					{
						// Create Profile Fields Table
						$sql = "CREATE TABLE IF NOT EXISTS `" . $profileFieldsTable . "` (
							`id` int unsigned NOT NULL auto_increment,
							`field_group_id` int unsigned NOT NULL default '0',
							`varname` varchar(50) NOT NULL DEFAULT '',
							`title` varchar(255) NOT NULL DEFAULT '',
							`hint` text NOT NULL DEFAULT '',
							`field_type` varchar(50) NOT NULL DEFAULT '',
							`field_size` int(3) NOT NULL default '0',
							`field_size_min` int(3) NOT NULL default '0',
							`required` int(1) NOT NULL default '0',
							`match` varchar(255) NOT NULL DEFAULT '',
							`range` varchar(255) NOT NULL DEFAULT '',
							`error_message` varchar(255) NOT NULL DEFAULT '',
							`other_validator` varchar(255) NOT NULL DEFAULT '',
							`default` varchar(255) NOT NULL DEFAULT '',
							`position` int(3) NOT NULL default '0',
							`visible` int(1) NOT NULL default '0',
							`related_field_name` varchar(255) DEFAULT NULL,
							PRIMARY KEY  (`id`),
							KEY `varname` (`varname`,`visible`)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ";

						$db->createCommand($sql)->execute();

						// Create Profiles Table
						$sql = "CREATE TABLE IF NOT EXISTS `" . $profileTable . "` (
							`id` int unsigned NOT NULL auto_increment,
							`user_id` int unsigned NOT NULL,
							`timestamp` timestamp NOT NULL,
							`privacy` ENUM('protected', 'private', 'public') NOT NULL,
							`lastname` varchar(50) NOT NULL default '',
							`firstname` varchar(50) NOT NULL default '',
							`show_friends` tinyint(1) DEFAULT 1,
							`allow_comments` tinyint(1) DEFAULT 1,
							`email` varchar(255) NOT NULL default '',
							`street` varchar(255),
							`city` varchar(255),
							`about` text,
							PRIMARY KEY  (`id`)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

						$db->createCommand($sql)->execute();

						$sql = "CREATE TABLE IF NOT EXISTS `" . $profileCommentTable ."` (
							`id` int(11) NOT NULL AUTO_INCREMENT,
							`user_id` int(11) NOT NULL,
							`profile_id` int(11) NOT NULL,
							`comment` text NOT NULL,
							`createtime` int(11) NOT NULL,
							PRIMARY KEY (`id`)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

						$db->createCommand($sql)->execute();

						$sql = "CREATE TABLE IF NOT EXISTS `".$profileVisitTable."` (
							`visitor_id` int(11) NOT NULL,
							`visited_id` int(11) NOT NULL,
							`timestamp_first_visit` int(11) NOT NULL,
							`timestamp_last_visit` int(11) NOT NULL,
							`num_of_visits` int(11) NOT NULL,
							PRIMARY KEY (`visitor_id`,`visited_id`)
								) ENGINE=InnoDB;";

						$db->createCommand($sql)->execute();
					}

					if(isset($_POST['installRole']))  
					{
						// Create Roles Table
						$sql = "CREATE TABLE IF NOT EXISTS `".$rolesTable."` (
							`id` INT unsigned NOT NULL AUTO_INCREMENT ,
							`title` VARCHAR(255) NOT NULL ,
							`description` VARCHAR(255) NULL,
							`is_membership_possible` tinyint(1) NOT NULL DEFAULT 0,
							`price` double COMMENT 'Price (when using membership module)',
							`duration` int COMMENT 'How long a membership is valid',
							PRIMARY KEY (`id`)) 
								ENGINE = InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ; ";

						$db->createCommand($sql)->execute();

						// Create User_has_role Table

						$sql = "CREATE TABLE IF NOT EXISTS `".$userRoleTable."` (
							`user_id` int unsigned NOT NULL,
							`role_id` int unsigned NOT NULL,
							PRIMARY KEY (`user_id`, `role_id`)
								) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

						$db->createCommand($sql)->execute();

						if(isset($_POST['installMessages'])) 
						{
							// Create Messages Table
							$sql = "
								CREATE TABLE IF NOT EXISTS `" . $messagesTable . "` (
										`id` int unsigned NOT NULL auto_increment,
										`timestamp` int unsigned NOT NULL,
										`from_user_id` int unsigned NOT NULL,
										`to_user_id` int unsigned NOT NULL,
										`title` varchar(255) NOT NULL,
										`message` text,
										`message_read` tinyint(1) NOT NULL,
										`answered` tinyint(1) NOT NULL,
										`draft` tinyint(1) default NULL,
										PRIMARY KEY  (`id`)
										) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;"; 

								$db->createCommand($sql)->execute();
						}

						$sql = "INSERT INTO `".$privacySettingTable."` (`user_id`) values (2)";
						$db->createCommand($sql)->execute();

						$sql = "INSERT INTO `".$usersTable."` (`id`, `username`, `password`, `activationKey`, `createtime`, `lastvisit`, `superuser`, `status`) VALUES
							(1, 'admin', '".YumUser::encrypt('admin')."', '', ".time().", 0, 1, 1),
							(2, 'demo', '".YumUser::encrypt('demo')."', '', ".time().", 0, 0, 1)";
						$db->createCommand($sql)->execute();

						if(isset($_POST['installMembership'])) {
							$sql = "insert into `{$paymentTable}` (`title`) values ('Prepayment'), ('Paypal')";

							$db->createCommand($sql)->execute();
						}

						if(isset($_POST['installRole']))
						{
							$sql = "INSERT INTO `".$actionTable ."` (`title`) VALUES 
								('message_write'),
								('message_receive'),
								('user_create'),
								('user_update'),
								('user_remove'),
								('user_admin')";

							$db->createCommand($sql)->execute();

							$sql = "INSERT INTO `{$permissionTable}` (`principal_id`, `subordinate_id`, `type`, `action`, `template`, `comment`) VALUES
								(2, 0, 'role', 1, 0, 'Users can write messagse'),
								(2, 0, 'role', 2, 0, 'Users can receive messagse'),
								(2, 0, 'role', 3, 0, 'Users are able to view visits of his profile'),
								(1, 0, 'role', 4, 0, ''),
								(1, 0, 'role', 5, 0, ''),
								(1, 0, 'role', 6, 0, ''),
								(1, 0, 'role', 7, 0, '');";

							$db->createCommand($sql)->execute();



							$sql = "INSERT INTO `".$rolesTable."` (`title`,`description`, `price`, `duration`) VALUES
								('UserManager', 'This users can manage Users', 0, 0),
								('Demo', 'Users having the demo role', 0, 0),
								('Business', 'Example Business account', 9.99, 7),
								('Premium', 'Example Premium account', 19.99, 28) ";
							$db->createCommand($sql)->execute();

							$sql = "INSERT INTO `".$userRoleTable."` (`user_id`, `role_id`) VALUES
								(2, 3)";
							$db->createCommand($sql)->execute();


						}
						if(isset($_POST['installProfiles']))
						{
							$sql = "INSERT INTO `".$profileTable."` (`id`, `user_id`, `lastname`, `firstname`, `email`) VALUES
								(1, 1, 'admin','admin','webmaster@example.com'),
								(2, 2, 'demo','demo','demo@example.com')";
							$db->createCommand($sql)->execute();

							$sql = "INSERT INTO `".$profileFieldsTable."` (`varname`, `title`, `field_type`, `field_size`, `required`, `visible`, `other_validator`) VALUES ('email', 'E-Mail', 'VARCHAR', 255, 1, 3, 'CEmailValidator'), ('firstname', 'First name', 'VARCHAR', 255, 1, 3, ''), ('lastname', 'Last name', 'VARCHAR', 255, 1, 3, ''), ('street','Street', 'VARCHAR', 255, 0, 3, ''), ('city','City', 'VARCHAR', 255, 0, 3, ''), ('about', 'About', 'TEXT', 255, 0, 3, '')";
							$db->createCommand($sql)->execute();

						}



						// Do it
						$transaction->commit();

						// Victory
						$this->render('success');
					} 
					else 
					{
						throw new CException(Yum::t('Database connection is not working'));	
					}
				}
			}
			else {
				$this->render('start', array(
							'usersTable' => Yum::resolveTableName($this->module->usersTable,Yii::app()->db),
							'privacySettingTable' => Yum::resolveTableName($this->module->privacySettingTable,Yii::app()->db),
							'textSettingsTable' => Yum::resolveTableName($this->module->textSettingsTable,Yii::app()->db),
							'rolesTable' => Yum::resolveTableName($this->module->rolesTable,Yii::app()->db),
							'membershipTable' => Yum::resolveTableName($this->module->membershipTable,Yii::app()->db),
							'paymentTable' => Yum::resolveTableName($this->module->paymentTable,Yii::app()->db),
							'messagesTable' => Yum::resolveTableName($this->module->messagesTable,Yii::app()->db),
							'profileTable' => Yum::resolveTableName($this->module->profileTable,Yii::app()->db),
							'profileCommentTable' => Yum::resolveTableName($this->module->profileCommentTable,Yii::app()->db),
							'profileVisitTable' => Yum::resolveTableName($this->module->profileVisitTable,Yii::app()->db),
							'profileFieldsTable' => Yum::resolveTableName($this->module->profileFieldsTable,Yii::app()->db),
							'userRoleTable' => Yum::resolveTableName($this->module->userRoleTable,Yii::app()->db),
							'usergroupTable' => Yum::resolveTableName($this->module->usergroupTable,Yii::app()->db),
							'usergroupMessagesTable' => Yum::resolveTableName($this->module->usergroupMessagesTable,Yii::app()->db),
							'permissionTable' => Yum::resolveTableName($this->module->permissionTable,Yii::app()->db),
							'friendshipTable' => Yum::resolveTableName($this->module->friendshipTable,Yii::app()->db),
							'actionTable' => Yum::resolveTableName($this->module->actionTable,Yii::app()->db),
							));
			}
		} else {
			throw new CException(Yum::t('User management module is not in Debug Mode'));	
		}
	}

	public function actionIndex()
	{
		$this->actionInstall();
	}
}
