<?php

class YumSettingsController extends YumController
{
	private $_model;

	public function filters()
	{
		return array(
			'accessControl', 
		);
	}

	public function accessRules()
	{
		return array(
			array('allow', 
				'actions'=>array('index', 'view', 'create', 'update','admin', 'delete', 'setActive'),
				'users'=>array('admin'),
			),
			array('deny', 
				'users'=>array('*'),
			),
		);
	}

	public function actionSetActive()
	{
		if(isset($_POST['active_profile'])) {
			foreach(YumSettings::model()->findAll() as $setting) {
				$setting->is_active = false;
				$setting->save();
			}
			$setting = YumSettings::model()->findByPk($_POST['active_profile']);
			$setting->is_active = true;
			$setting->save();	
		}
		$this->redirect(array($_POST['returnTo']));
	}

	public function actionCreate()
	{
		$this->layout = YumWebModule::yum()->adminLayout;
		$model=new YumSettings;

		$this->performAjaxValidation($model);

		if(isset($_POST['YumSettings'])) {
			$model->attributes = $_POST['YumSettings'];

			if($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render('create',array(
			'model'=>$model,
		));
	}

	public function actionUpdate()
	{
		$this->layout = YumWebModule::yum()->adminLayout;
		if(!isset($_GET['id']))	
			$_GET['id'] = 0;
		if($_GET['id'] == 0)
			$_GET['id'] = YumSettings::model()->getActive();
		$model=$this->loadModel();

		$this->performAjaxValidation($model);

		$YumSettingsData = Yii::app()->request->getPost('YumSettings');
		if($YumSettingsData !== null)
		{
			$model->attributes = $YumSettingsData;


			if($model->save())
				$this->redirect(array('index'));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			$this->loadModel()->delete();

			if(Yii::app()->request->getQuery('ajax') === null)
			{
				$this->redirect(array('index'));
			}
		}
		else
			throw new CHttpException(400,
					Yii::t('app', 'Invalid request. Please do not repeat this request again.'));
	}

	public function actionIndex()
	{
		$model=new YumSettings('search');
		$model->unsetAttributes();

		$YumSettingsData = Yii::app()->request->getQuery('YumSettings');
		if($YumSettingsData !== null)
			$model->attributes = $YumSettingsData;

		$this->render('admin',array(
			'model'=>$model,
		));
	}

	public function loadModel()
	{
		if($this->_model===null)
		{
			$id = Yii::app()->request->getQuery('id');
			if(!empty($id))
				$this->_model = YumSettings::model()->findbyPk($id);

			if($this->_model===null)
				throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
		}
		return $this->_model;
	}

	protected function performAjaxValidation($model)
	{
		$ajax = Yii::app()->request->getPost('ajax'); 
		if($ajax == 'yum-settings-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
