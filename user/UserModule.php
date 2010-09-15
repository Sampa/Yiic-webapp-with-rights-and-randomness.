<?php

Yii::setPathOfAlias('YumModule' , dirname(__FILE__));
Yii::setPathOfAlias('YumComponents' , dirname(__FILE__) . '/components/');
Yii::setPathOfAlias('YumAssets' , dirname(__FILE__) . '/assets/');
Yii::import('YumModule.models.*');
Yii::import('YumModule.controllers.YumController');

class UserModule extends YumWebModule
{
	public $version = '0.8';
	public $debug = false;
	public $usersTable = 'users';
	public $settingsTable = 'yumsettings';
	public $textSettingsTable = 'yumtextsettings';
	public $messagesTable = 'messages';
	public $profileFieldsTable = 'profile_fields';
	public $profileFieldsGroupTable = 'profile_fields_group';
	public $profileTable = 'profiles';
	public $profileVisitTable = 'profile_visit';
	public $rolesTable = 'roles';
	public $userRoleTable = 'user_has_role';
	public $userUserTable = 'user_has_user';
	public $roleRoleTable = 'role_has_role';
	public $installDemoData = true;
	public $preserveProfiles = true;
	public $baseLayout = 'application.views.layouts.main';
	public $layout = 'yum';
	public $loginLayout = 'yum';
	public $adminLayout = 'yum';
	public $profileLayout = 'yumprofile';
	public $profileView = '/profile/view';
	public $useYiiCheckAccess = false;
	public $registrationType = YumRegistration::REG_EMAIL_AND_ADMIN_CONFIRMATION;
	public $allowRecovery = true;
	public $enableRoles = true;
	public $enableProfiles = true;
	public $mail_send_method = 'Instant';
	public $password_expiration_time = 30;
	public $enableAvatars = true;
	public $avatarPath = 'images';
	public $mailer = 'yum'; // set to swift to active emailing by swiftMailer

	// determines whether configuration by Database Table is enabled or disabled
	public $tableSettingsDisabled = false;

	// Messaging System can be MSG_NONE, MSG_PLAIN or MSG_DIALOG
	public $messageSystem = YumMessage::MSG_DIALOG;

	public $salt = '';
	 // valid callback function for password hashing ie. sha1
	public $hashFunc = 'md5';	

	public $yumBaseRoute = '/user';

	public static $dateFormat = "m-d-Y";  //"d.m.Y H:i:s"
	public $dateTimeFormat = 'm-d-Y G:i:s';  //"d.m.Y H:i:s"

	private $_urls=array(
		'registration'=>array('//user/registration/'),
		'recovery'=>array('//user/registration/recovery'),
		'login'=>array('//user/user'),
		'return'=>false,
		// Page to go after admin logs in
		'returnAdmin'=>array('//user/statistics/index'),
		// Page to go to after logout
		'returnLogout'=>array('//user/user/login'),
	);

	// Activate profile History (profiles are kept always, and when the 
  // user changes his profile, it gets added to the database rather than
  // updated).
	public $profileHistory = true;
	
	// When readOnlyProfiles is set, only administrators can update Profile
  // Information
	public $readOnlyProfiles = false;

	// When forceProtectedProfiles is set, only administrators and the user 
  // himself can view the profile 
	public $forceProtectedProfiles = false;

	// LoginType :
	// 0: Allow login only by Username
	const LOGIN_BY_USERNAME		= 0;
	// 1: Allow login only by E-Mail (needs profile module)
	const LOGIN_BY_EMAIL			= 1; 
	// 2: Allow login by E-Mail or Username (needs profile module)
	const	LOGIN_BY_USERNAME_OR_EMAIL	= 2; 
	// 3: Allow login only by OpenID (TODO FIXME needs to be implemented) 
	//const LOGIN_OPENID		= 4;
	public $loginType = self::LOGIN_BY_USERNAME;
	
	/**
	 * Whether to use captcha e.g. in registration process
	 * @var boolean
	 */
	public $enableCaptcha = true;
	
	/**
	 * Defines all Controllers of the User Management Module and maps them to
	 * shorter terms for using in the url
	 * @var array
	 */
	public $controllerMap=array(
		'default'=>array('class'=>'YumModule.controllers.YumDefaultController'),
		'install'=>array('class'=>'YumModule.controllers.YumInstallController'),
		'registration'=>array('class'=>'YumModule.controllers.YumRegistrationController'),
		'statistics'=>array('class'=>'YumModule.controllers.YumStatisticsController'),
		'hierarchy'=>array('class'=>'YumModule.controllers.YumHierarchyController'),
		'user'=>array('class'=>'YumModule.controllers.YumUserController'),	
		// workaround to allow the url application/user/login: 
		'login'=>array('class'=>'YumModule.controllers.YumUserController'),	
		'role'=>array('class'=>'YumModule.controllers.YumRoleController'),	
		'messages'=>array('class'=>'YumModule.controllers.YumMessagesController'),	
		'profile'=>array('class'=>'YumModule.controllers.YumProfileController'),	
		'fields'=>array('class'=>'YumModule.controllers.YumFieldsController'),	
		'fieldsgroup'=>array('class'=>'YumModule.controllers.YumFieldsGroupController'),	
	);

	public $passwordRequirements = array(
			'minLen' => 8,
			'maxLen' => 32,
			'minLowerCase' => 1,
			'minDigits' => 1,
			'minDigits' => 1,
			'maxRepetition' => 3,
			);

	/**
	 * Additionally implements support for getting URLs
	 * @param string $name
	 */
	public function __get($name)
	{
		if(substr($name,-3) === 'Url')
			if(isset($this->_urls[substr($name, 0, -3)]))
				return Yum::route($this->_urls[substr($name, 0, -3)]);
				
		return parent::__get($name);
	}

	/**
	 * Additionally implements support for setting URLs
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name,$value)
	{
		if(substr($name,-3)==='Url')
		{
			if(isset($this->_urls[substr($name,0,-3)]))
				$this->_urls[substr($name,0,-3)]=$value;		
		}
		//parent::__set($name,$value);
	}

	public function init()
	{
		$this->setImport(array(
			'YumModule.models.*',
			'YumModule.components.*',
			'YumModule.core.*',
		));
	}

	public function beforeControllerAction($controller, $action) {
		parent::beforeControllerAction($controller, $action);

		if(Yii::app()->user->isAdmin())
			$controller->layout = Yii::app()->getModule('user')->adminLayout;

		// Assign options from settings table, if available
		if(Yii::app()->controller->id != 'install')
		try {
			$settings = YumSettings::model()->find('is_active');
			
			$options = array('preserveProfiles', 'registrationType', 'enableRecovery',
					'readOnlyProfiles', 'messageSystem', 'loginType',
					'mail_send_method', 'password_expiration_time', 'enableCaptcha');
			foreach($options as $option) 
				$this->$option = $settings->$option;
		} catch (CDbException $e) {
			$this->tableSettingsDisabled = true;
		}
		return true;
	}

	/**
	 * Configures the module with the specified configuration.
	 * Override base class implementation to allow static variables.
	 * @param array the configuration array
	 */
	public function configure($config)
	{
		if(is_array($config)) {
			foreach($config as $key=>$value) {
				if(isset(UserModule::${$key})) {
					UserModule::${$key} = $value;
				} else 
					$this->$key=$value;
			}
		}
	}

}
