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
	public static $returnUrl = array("user/profile");		// Page to go to after registration, login etc.
	public static $returnLogoutUrl = array("user/login");
	
	// LoginType :
  // 0: Allow login only by Username
  // 1: Allow login only by E-Mail
  // 2: Allow login by E-Mail or Username
  // 3: Allow login only by OpenID 
	public $loginType = 2;


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
