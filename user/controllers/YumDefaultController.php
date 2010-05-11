<?php

class YumDefaultController extends YumController 
{
	/**
	 * @return array of arrays
	 */
	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('index'),
				'users'=>array('*'),
			),
			#deny all other users
			array('deny',  
				'users'=>array('*'),
			),
		);
	}
	
	public function actionIndex() 
	{
		$this->redirect($this->module->returnUrl);
	}
}

?>
