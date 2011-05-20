<?php
Yii::setPathOfAlias('YumModule' , dirname(__FILE__));
Yii::setPathOfAlias('YumComponents' , dirname(__FILE__) . '/components/');
Yii::setPathOfAlias('YumAssets' , dirname(__FILE__) . '/assets/');

Yii::import('YumModule.models.*');
Yii::import('YumModule.controllers.YumController');

class UserModule extends CWebModule {
	public $version = '0.8-rc2';
	public $debug = false;

	//layout related control vars
	public $baseLayout = 'application.views.layouts.main';
	public $layout = 'application.modules.user.views.layouts.yum';
	public $loginLayout = 'application.modules.user.views.layouts.yum';
	public $adminLayout = 'application.modules.user.views.layouts.yum';

	// configuration related control vars

	public $enableLogging = true;
	public $enableOnlineStatus = true; 
	
	// After how much seconds without an action does a user gets indicated as 
	// offline? Note that, of course, clicking the Logout button will indicate
	// him as offline instantly anyway.
	public $offlineIndicationTime = 3600; // 5 Minutes

	// set to false to enable case insensitive users.
  // for example, demo and Demo would be the same user then
	public $caseSensitiveUsers = true;

	// set this to false if you do not want to access data through a REST
	// api
	public $enableRESTapi = true;

	public $password_expiration_time = 30; // days
	public $activationPasswordSet = false;
	public $autoLogin = false;
	public $activateFromWeb = true;
	public $recoveryFromWeb = false;
	public $mailer = 'yum'; // set to swift to active emailing by swiftMailer or PHPMailer to use PHPMailer as emailing lib.
	public $phpmailer = null; // PHPMailer array options.

	public $facebookConfig = false;
	public $pageSize = 10;

	// if you want the users to be able to edit their profile TEXTAREAs with an
	// rich text Editor like CKEditor, set that here
  public $rtepath = false; // Don't use an Rich text Editor
  public $rteadapter = false; // Don't use an Adapter

	public $salt = '';
	 // valid callback function for password hashing ie. sha1
	public $hashFunc = 'md5';

	// Set this to true to really remove users instead of setting the status
	// to -2 (YumUser::REMOVED)
	public $trulyDelete = false;

	public $yumBaseRoute = '/user';

	public static $dateFormat = "m-d-Y";  //"d.m.Y H:i:s"
	public $dateTimeFormat = 'm-d-Y G:i:s';  //"d.m.Y H:i:s"

	// Use this to set dhcpOptions if using authentication over LDAP
	public $ldapOptions = array(
			'ldap_host' => '', 
			'ldap_port' => '', 
			'ldap_basedn' => '',
			'ldap_protocol' => '',
			'ldap_autocreate' => '',
			'ldap_tls' => '',
			'ldap_transfer_attr' => '',
			'ldap_transfer_pw' => '');

	private $_urls=array(
			'login'=>array('//user/user'),
			'return'=>array('//profile/profile/view'),
			'firstVisit'=>array('//user/privacy/update'),
			// Page to go after admin logs in
			'returnAdmin'=>array('//user/statistics/index'),
			// Page to go to after logout
			'returnLogout'=>array('//user/user/login'));

	private $_views = array(
			'login' => '/user/login',
			'menu' => '/user/menu',
			'registration' => '/registration/registration',
			'activate' => '/user/resend_activation',
			'message' => '/user/message',
			'recovery' => '/registration/recovery',
			'passwordForm' => '/user/_activation_passwordform',
			'recoveryChangePassword' =>'/user/changepassword',
			'messageCompose' =>'application.modules.user.views.messages.compose');

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
	 * Defines all Controllers of the User Management Module and maps them to
	 * shorter terms for using in the url
	 * @var array
	 */
	public $controllerMap=array(
		'default'=>array('class'=>'YumModule.controllers.YumDefaultController'),
		'rest'=>array('class'=>'YumModule.controllers.YumRestController'),
		'auth'=>array('class'=>'YumModule.controllers.YumAuthController'),
		'install'=>array('class'=>'YumModule.controllers.YumInstallController'),
		'statistics'=>array('class'=>'YumModule.controllers.YumStatisticsController'),
		'user'=>array('class'=>'YumModule.controllers.YumUserController'),
		// workaround to allow the url application/user/login:
		'login'=>array('class'=>'YumModule.controllers.YumUserController')
	);

	// Table names
	private $_tables = array(
			'users' => 'users',
			'privacySetting' => 'privacysetting',
			'textSettings' => 'yumtextsettings',
			'messages' => 'messages',
			'usergroup' => 'usergroup',
			'usergroupMessages' => 'usergroup_messages',
			'profileFields' => 'profile_fields',
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
		// Do not enable Debug mode when in Production Mode
		if(!defined('YII_DEBUG'))
			$this->debug = false;

		if(Yii::app()->user->isAdmin())
			$controller->layout = Yum::module()->adminLayout;
		
		return parent::beforeControllerAction($controller, $action);
	}

}
