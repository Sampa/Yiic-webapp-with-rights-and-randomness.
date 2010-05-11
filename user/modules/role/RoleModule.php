<?php

class RoleModule extends CWebModule
{
	
	public $version = '0.5';
	public $debug = false;
	public $rolesTable = "{{roles}}";
	public $userRoleTable = "{{user_has_role}}";
	public $installDemoData = true;
	
	public function init()
	{
		$this->setImport(array(
			'user.modules.role.models.*',
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
