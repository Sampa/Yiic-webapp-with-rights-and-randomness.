<?php

class UserModule extends CWebModule
{
	public static $dateFormat = "m-d-Y";  //"d.m.Y H:i:s"
	
	public $version = '0.4';
	public $debug = false;
	public $usersTable = "users";
	public $messagesTable = "messages";
	public $profileFieldsTable = "profile_fields";
	public $profileTable = "profiles";
	public $rolesTable = "roles";
	public $userRoleTable = "user_has_role";
	public $installDemoData = true;
	public $layout = 'column2';

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
}
