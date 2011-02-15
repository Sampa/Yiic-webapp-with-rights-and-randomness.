<?php
// This is the entry script of the Yii User Management Module
// You can see all default configuration options defined here

Yii::setPathOfAlias('YumModule' , dirname(__FILE__));
Yii::setPathOfAlias('YumComponents' , dirname(__FILE__) . '/components/');
Yii::setPathOfAlias('YumAssets' , dirname(__FILE__) . '/assets/');
Yii::import('YumModule.models.*');
Yii::import('YumModule.controllers.YumController');

class UserModule extends CWebModule {
	public $version = '0.8-svn';
	public $debug = false;

	//layout related control vars
	public $baseLayout = 'application.views.layouts.main';
	public $layout = 'yum';
	public $loginLayout = 'yum';
	public $adminLayout = 'yum';
	public $profileLayout = 'yumprofile';

	//configuration related control vars

	// set useYiiCheckAccess to true to disable Yums own checkAccess routines.
  // Use this when you implement your own access logic or use yum together with
  // SrBAC
	public $useYiiCheckAccess = false;

	public $enableRegistration = true;
	public $enableRecovery = true;
	public $enableRoles = true;
	public $enableProfiles = true;
	public $enableProfileComments = true;
	public $enableFriendship = true;
	public $enableLogging = true;

	public $enableMembership = true;

	public $enableOnlineStatus = true; 
	
	// After how much seconds without an action does a user gets indicated as 
	// offline? Note that, of course, clicking the Logout button will indicate
	// him as offline instantly anyway.
	public $offlineIndicationTime = 3600; // 5 Minutes

	// Whether to confirm the activation of an user by email
	public $enableActivationConfirmation = true; 

	// set to false to enable case insensitive users.
  // for example, demo and Demo would be the same user then
	public $caseSensitiveUsers = true;

	/* Avatar options */
	// Enable the possibility for users to upload an avatar image. The
	// image then gets displayed at his profile, his messages and his
	// profile comments.
	public $enableAvatar = true;
	public $enableUsergroups = true;
	// Where to save the avatar images? (Yii::app()->baseUrl . $avatarPath)	
	public $avatarPath = 'images';

	// Maximum width of avatar in pixels. Correct aspect ratio should be set up
	// by CImageModifier automatically
	// Set to 0 to disable image size check
	public $avatarMaxWidth = 200;
	public $avatarThumbnailWidth = 50; // For display in user browse, friend list
	public $avatarDisplayWidth = 200;

	public $password_expiration_time = 30; // days
	public $activationPasswordSet = false;
	public $autoLogin = false;
	public $activateFromWeb = true;
	public $recoveryFromWeb = false;
	public $mailer = 'yum'; // set to swift to active emailing by swiftMailer or PHPMailer to use PHPMailer as emailing lib.
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
		'registration'=>array('//user/registration/registration'),
		'recovery'=>array('//user/registration/recovery'),
		'login'=>array('//user/user'),
		'return'=>array('//user/profile/view'),
		'firstVisit'=>array('//user/privacy/update'),
		// Page to go after admin logs in
		'returnAdmin'=>array('//user/statistics/index'),
		// Page to go to after logout
		'returnLogout'=>array('//user/user/login'));

	private $_views = array(
			'login' => '/user/login',
			'profile' => '/profile/view',
			'profileComment' => '/profileComment/_view',
			'profileEdit' => '/profile/update',
			'privacysetting' => '/privacy/update',
			'menu' => '/user/menu',
			'registration' => '/registration/registration',
			'activate' => '/user/resend_activation',
			'message' => '/user/message',
			'recovery' => '/registration/recovery',
			'passwordForm' => '/user/_activation_passwordform',
			'activationSuccess' => '/registration/activation_success',
			'activationFailure' => '/registration/activation_failure',
			'recoveryChangePassword' =>'/user/changepassword',
			'messageCompose' =>'application.modules.user.views.messages.compose',
			'membershipExpired' =>'/membership/membership_expired');

	public $profilesViewableByGuests = false;

	// LoginType :
	const LOGIN_BY_USERNAME		= 1;
	const LOGIN_BY_EMAIL		= 2;
	const LOGIN_BY_OPENID		= 4;
	const LOGIN_BY_FACEBOOK		= 8;
	const LOGIN_BY_TWITTER		= 16;
	const LOGIN_BY_LDAP			= 32;
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
			'activity' => 'activities',
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
		'maxLen'=>30,
		'match' => '/^[A-Za-z0-9_]+$/u',
		'dontMatchMessage' => 'Incorrect symbol\'s. (A-z0-9)',
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

		// Do not enable Debug mode when in Production Mode
		if(!defined('YII_DEBUG'))
			$this->debug = false;

		if(Yii::app()->user->isAdmin())
			$controller->layout = Yum::module()->adminLayout;

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
