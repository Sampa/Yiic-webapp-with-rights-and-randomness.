<?php

class YumHierarchyController extends YumController
{
	const PAGE_SIZE=10;
	private $_model;

	public function accessRules()
	{
		return array(
				array('allow',
					'actions'=>array('index'),
					'expression'=>'Yii::app()->user->isAdmin()',
					),
				array('deny',  // deny all other users
						'users'=>array('*'),
						),
				);
	}

	public function actionIndex()
	{
		$this->render('/hierarchy/view');		
	}
}
