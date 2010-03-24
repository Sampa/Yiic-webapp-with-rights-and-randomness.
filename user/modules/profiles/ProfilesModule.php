<?php

class ProfilesModule extends CWebModule
{
	
	public $version = '0.5';
	public $debug = false;
	public $profileFieldsTable = "profile_fields";
	public $profileTable = "profiles";
	public $installDemoData = true;

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
