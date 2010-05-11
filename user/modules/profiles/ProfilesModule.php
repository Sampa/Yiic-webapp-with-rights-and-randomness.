<?php

Yii::setPathOfAlias( 'YumProfileModule' , dirname(__FILE__) );

class ProfilesModule extends CWebModule
{
	
	public $version = '0.5';
	public $debug = false;
	public $profileFieldsTable = "{{profile_fields}}";
	public $profileTable = "{{profiles}}";
	public $installDemoData = true;
	
	public $controllerMap=array(
		'fields'=>array('class'=>'YumProfileModule.controllers.YumFieldsController'),
	);	

	public function init()
	{
		$parentImport=$this->getParentModule() instanceof UserModule 
			? array(
				'YumModule.models.*',
			)
			: array();
		$import=array('YumProfileModule.models*');
		$this->setImport(CMap::mergeArray($parentImport,$import));
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
