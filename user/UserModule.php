<?php

class UserModule extends CWebModule
{
	
	public $version = '0.5';
	public $debug = false;
	public $usersTable = "users";
	public $messagesTable = "messages";
	public $profileFieldsTable = "profile_fields";
	public $profileTable = "profiles";
	public $rolesTable = "roles";
	public $userRoleTable = "user_has_role";
	public $userUserTable = "user_has_user";
	public $installDemoData = true;
	public $disableEmailActivation = false;
	public $layout = 'column2';
	public $salt = '';
	public $hashFunc = 'md5'; // valid callback function for password hashing ie. sha1
	
	public static $dateFormat = "m-d-Y";  //"d.m.Y H:i:s"
	public static $allowInactiveAcctLogin=false;

	public static $registrationUrl = array("user/registration");
	public static $recoveryUrl = array("user/recovery");
	public static $loginUrl = array("user/login");

	// Page to go to after registration, login etc.
	public static $returnUrl = array("user/profile");	
	public static $returnLogoutUrl = array("user/login");

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

	public function init()
	{
		$this->setImport(array(
			'user.models.*',
			'user.components.*',
		));
	}


	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			return true;
		}
		else
			return false;
	}

	/**
	 * Configures the module with the specified configuration.
	 * Override base class implementation to allow static variables.
	 * @param array the configuration array
	 */
	public function configure($config)
	{
		if(is_array($config))
		{
			foreach($config as $key=>$value)
			{
				if(isset(UserModule::${$key}))
				{
					UserModule::${$key} = $value;
				}
				else 
					$this->$key=$value;
			}
		}
	}

	/** 
	 * Checks if the requested module is a submodule of the user module 
	 */
	public function hasModule($module)
	{
		return in_array($module, array_keys($this->getModules()));
	}


}
