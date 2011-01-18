<?php

Yii::setPathOfAlias('YumModule' , dirname(__FILE__));
Yii::setPathOfAlias('YumComponents' , dirname(__FILE__) . '/components/');
Yii::setPathOfAlias('YumAssets' , dirname(__FILE__) . '/assets/');
Yii::import('YumModule.models.*');
Yii::import('YumModule.controllers.YumController');

class UserModule extends CWebModule {
	public $version = '0.8';
	public $debug = false;

	// database related control vars
	public $installDemoData = true;

	//layout related control vars
	public $baseLayout = 'application.views.layouts.main';
	public $layout = 'yum';
	public $loginLayout = 'yum';
	public $adminLayout = 'yum';
	public $profileLayout = 'yumprofile';

	//configuration related control vars
	public $preserveProfiles = true;
	public $useYiiCheckAccess = false;
	public $registrationType = YumRegistration::REG_EMAIL_AND_ADMIN_CONFIRMATION;
	public $allowRecovery = true;
	public $enableRoles = true;
	public $enableMembership = true;
	public $enableProfiles = true;
	public $enableProfileComments = true;
	public $enableFriendship = true;
	public $enableLogging = true;
	public $enableUsergroups = true;
	public $enableOnlineStatus = true; 

	// set to false to enable case insensitive users.
  // for example, demo and Demo would be the same user then
	public $caseSensitiveUsers = true;

	/* Avatar options */
	// Enable the possibility for users to upload an avatar image. The
	// image then gets displayed at his profile, his messages and his
	// profile comments.
	public $enableAvatar = true;

	// Where to save the avatar images? (Yii::app()->baseUrl . $avatarPath)	
	public $avatarPath = 'images';

	// Use CImageModifier to scale uploaded avatar images to the specified
	// size. If this is set to false, the image will be forced to be 
	// between 50px and $avatarMaxWidth
	public $avatarScaleImage = true;

	// Maximum width of avatar in pixels. Correct aspect ratio should be set up
	// by CImageModifier automatically
	// Set to 0 to disable image size check
	public $avatarMaxWidth = 200;
	public $avatarDisplayWidth = 200;

	// Some default options for the CImageModifier
	public $imageModifierOptions = array(
			'upload_max_filesize' => 9999999, // 9,99 MB
			'file_max_size' => 9999999, // 9,99 MB
			'file_src_size' => 9999999, // 9,99 MB
			);

	public $password_expiration_time = 30;
	public $activationPasswordSet = false;
	public $autoLogin = false;
	public $activateFromWeb = true;
	public $recoveryFromWeb = false;

	// set $mailer to swift to active emailing by swiftMailer or PHPMailer to 
	// use PHPMailer as emailing lib.
	public $mailer = 'yum';
 	public $phpmailer = null; // PHPMailer array options.

	public $registrationEmail='register@website.com';
	public $recoveryEmail='restore@website.com';
	public $facebookConfig = false;
	public $pageSize = 10;

	// System-wide configuration option on how users should be notified about
  // new internal messages by email. Available options:
  // None, Digest, Instant, User, Treshhold
	// 'User' means to use the user-specific option in the user table
	public $notifyType = 'user';

	// Send a message to the user if the email changing has been succeeded
	public $notifyEmailChange = true;

	// if you want the users to be able to edit their profile TEXTAREAs with an
	// rich text Editor like CKEditor, set that here
  public $rtepath = false; // Don't use an Rich text Editor
  public $rteadapter = false; // Don't use an Adapter

	// determines whether configuration by Database table is enabled or disabled
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
		'return'=>array('//user/profile/view'),
		// Page to go after admin logs in
		'returnAdmin'=>array('//user/statistics/index'),
		// Page to go to after logout
		'returnLogout'=>array('//user/user/login'));

	private $_views = array(
			'login' => '/user/login',
			'profile' => '/profile/view',
			'profileComment' => '/profileComment/_view',
			'profileEdit' => '/profile/update',
			'menu' => '/user/menu',
			'registration' => '/user/registration',
			'activate' => '/user/resend_activation',
			'message' => '/user/message',
			'recovery' => '/user/recovery',
			'passwordForm' => '/user/_activation_passwordform',
			'activationSuccess' => '/user/activation_success',
			'recoveryChangePassword' =>'/user/changepassword');

	// Activate profile History (profiles are kept always, and when the
  // user changes his profile, it gets added to the database rather than
  // updated).
	public $enableProfileHistory = true;

	// When readOnlyProfiles is set, only administrators can update Profile
  // Information
	public $readOnlyProfiles = false;

	// When forceProtectedProfiles is set, only administrators and the user
  // himself can view the profile
	public $forceProtectedProfiles = false;
	public $profilesViewableByGuests = false;

	// LoginType :
	const LOGIN_BY_USERNAME		= 1;
	const LOGIN_BY_EMAIL			= 2;
	const LOGIN_BY_OPENID			= 4;
	const LOGIN_BY_FACEBOOK		= 8;
	const LOGIN_BY_TWITTER		= 16;
	const LOGIN_BY_LDAP				= 32;
	// Allow username and email login by default
	public $loginType = 3;

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
		'auth'=>array('class'=>'YumModule.controllers.YumAuthController'),
		'action'=>array('class'=>'YumModule.controllers.YumActionController'),
		'activities'=>array('class'=>'YumModule.controllers.YumActivityController'),
		'permission'=>array('class'=>'YumModule.controllers.YumPermissionController'),
		'comments'=>array('class'=>'YumModule.controllers.YumProfileCommentController'),
		'avatar'=>array('class'=>'YumModule.controllers.YumAvatarController'),
		'install'=>array('class'=>'YumModule.controllers.YumInstallController'),
		'registration'=>array('class'=>'YumModule.controllers.YumRegistrationController'),
		'statistics'=>array('class'=>'YumModule.controllers.YumStatisticsController'),
		'user'=>array('class'=>'YumModule.controllers.YumUserController'),
		'privacy'=>array('class'=>'YumModule.controllers.YumPrivacysettingController'),
		'groups'=>array('class'=>'YumModule.controllers.YumUsergroupController'),
		// workaround to allow the url application/user/login:
		'login'=>array('class'=>'YumModule.controllers.YumUserController'),
		'role'=>array('class'=>'YumModule.controllers.YumRoleController'),
		'membership'=>array('class'=>'YumModule.controllers.YumMembershipController'),
		'payment'=>array('class'=>'YumModule.controllers.YumPaymentController'),
		'messages'=>array('class'=>'YumModule.controllers.YumMessagesController'),
		'profile'=>array('class'=>'YumModule.controllers.YumProfileController'),
		'fields'=>array('class'=>'YumModule.controllers.YumFieldsController'),
		'friendship'=>array('class'=>'YumModule.controllers.YumFriendshipController'),
		'fieldsgroup'=>array('class'=>'YumModule.controllers.YumFieldsGroupController'),
	);

	// Table names
	private $_tables = array(
			'users' => 'users',
			'privacySetting' => 'privacysetting',
			'settings' => 'yumsettings',
			'textSettings' => 'yumtextsettings',
			'messages' => 'messages',
			'usergroup' => 'usergroup',
			'userUsergroup' => 'user_has_usergroup',
			'profileFields' => 'profile_fields',
			'profileFieldsGroup' => 'profile_fields_group',
			'profile' => 'profiles',
			'profileComment' => 'profile_comment',
			'profileVisit' => 'profile_visit',
			'roles' => 'roles',
			'membership' => 'membership',
			'payment' => 'payment',
			'friendship' => 'friendship',
			'permission' => 'permission',
			'action' => 'action',
			'userRole' => 'user_has_role',
			'activity' => 'activities',
			);


	public $passwordRequirements = array(
			'minLen' => 8,
			'maxLen' => 32,
			'minLowerCase' => 1,
			'minUpperCase'=>0,
			'minDigits' => 1,
			'maxRepetition' => 3,
			);

	public $usernameRequirements=array(
		'minLen'=>3,
		'maxLen'=>20,
		);

	/**
	 * Implements support for getting URLs, Tables and Views
	 * @param string $name
	 */
	public function __get($name) {
		if(substr($name, -3) === 'Url')
			if(isset($this->_urls[substr($name, 0, -3)]))
				return Yum::route($this->_urls[substr($name, 0, -3)]);

		if(substr($name, -4) === 'View')
			if(isset($this->_views[substr($name, 0, -4)]))
				return $this->_views[substr($name, 0, -4)];

		if(substr($name, -5) === 'Table')
			if(isset($this->_tables[substr($name, 0, -5)]))
				return $this->_tables[substr($name, 0, -5)];

		return parent::__get($name);
	}

	/**
	 * Implements support for setting URLs and Views
	 * @param string $name
	 * @param mixed $value
	 */
	public function __set($name,$value) {
		if(substr($name,-3)==='Url') {
			if(isset($this->_urls[substr($name,0,-3)]))
				$this->_urls[substr($name,0,-3)]=$value;
		}
		if(substr($name,-4)==='View') {
			if(isset($this->_views[substr($name,0,-4)]))
				$this->_views[substr($name,0,-4)]=$value;
		}
		if(substr($name,-5)==='Table') {
			if(isset($this->_tables[substr($name,0,-5)]))
				$this->_tables[substr($name,0,-5)]=$value;
		}

		//parent::__set($name,$value);
	}

	public function init() {
		$this->setImport(array(
			'YumModule.controllers.*',
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
		if(Yii::app()->controller->id != 'install' 
				&& !Yum::module()->tableSettingsDisabled)
			try {
				$settings = YumSettings::model()->find('is_active');

				$options = array('preserveProfiles', 'registrationType', 'enableRecovery',
						'readOnlyProfiles', 'enableProfileHistory' ,'messageSystem',
						'loginType', 'enableAvatar', 'notifyType',
						'password_expiration_time', 'enableCaptcha');
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
