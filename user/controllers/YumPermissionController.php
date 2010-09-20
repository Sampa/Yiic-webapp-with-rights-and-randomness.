<?php

class YumPermissionController extends YumController
{
	public $defaultAction = 'index';
	private $_model;

	public function accessRules()
	{
		return array(
				array('allow',
					'actions'=>array('index', 'view', 'create', 'update'),
					'users'=>array('@'),
					),
				array('deny',  // deny all other users
						'users'=>array('*'),
						),
				);
	}

	public function actionIndex() {
	}


	public function actionView() {
	}

	public function actionCreate() {
		$model=new YumPermission;

		if(isset($_POST['ajax']) && $_POST['ajax']==='permission-create-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		if(isset($_POST['YumPermission']))
		{
			$model->attributes=$_POST['YumPermission'];
			if($model->validate())
			{
				// form inputs are valid, do something here
				return;
			}
		}
		$this->render('create',array('model'=>$model));

	}

}
