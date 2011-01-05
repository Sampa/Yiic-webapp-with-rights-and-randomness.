<?php

class YumTextSettingsController extends YumController
{
	private $_model;

	public function accessRules()
	{
		return array(
				array('allow',  
					'actions'=>array('index','view'),
					'users'=>array('*'),
					),
				array('allow', 
					'actions'=>array('create','update'),
					'users'=>array('@'),
					),
				array('allow', 
					'actions'=>array('admin','delete'),
					'users'=>array('admin'),
					),
				array('deny', 
					'users'=>array('*'),
					),
				);
	}

	public function actionView()
	{
		$this->render('/textsettings/view',array(
					'model'=>$this->loadModel(),
					));
	}

	public function actionCreate()
	{
		$model=new YumTextSettings;

		foreach($_POST as $key => $value) {
			if(is_array($value))
				$_SESSION[$key] = $value;
		}

		if(isset($_SESSION['YumTextSettings'])) 
			$model->attributes = $_SESSION['YumTextSettings'];

		$this->performAjaxValidation($model);

		$YumTextSettingsData = Yii::app()->request->getPost('YumTextSettings');
		if($YumTextSettingsData !== null)
		{
			$model->attributes = $YumTextSettingsData;


			if($model->save()) {
				if(Yum::module()->enableLogging) {
					$user= YumUser::model()->findbyPK(Yii::app()->user->id);
					YumActivityController::logActivity($user, 'text_settings_created');
				}

					$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('/textsettings/create',array(
					'model'=>$model,
					));
	}

	public function actionUpdate()
	{
		$model=$this->loadModel();

		$this->performAjaxValidation($model);

		$YumTextSettingsData = Yii::app()->request->getPost('YumTextSettings');
		if($YumTextSettingsData !== null)
		{
			$model->attributes = $YumTextSettingsData;


			if($model->save())
			{
				if(Yum::module()->enableLogging == true)
				{
					$user= YumUser::model()->findbyPK(Yii::app()->user->id);
					YumActivityController::logActivity($user, 'text_settings_updated');
				}
				$this->redirect(array('view','id'=>$model->id));
			}}

			$this->render('/textsettings/update',array(
						'model'=>$model,
						));
	}

	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			if(Yum::module()->enableLogging == true)
			{
				$user= YumUser::model()->findbyPK(Yii::app()->user->id);
				YumActivityController::logActivity($user, 'text_settings_removed');
			}
			$this->loadModel()->delete();

			if(Yii::app()->request->getQuery('ajax') === null)
			{
				$returnUrl = Yii::app()->request->getPost('returnUrl');
				$this->redirect(!empty($returnUrl) ? $returnUrl : array('admin'));
			}
		}
		else
			throw new CHttpException(400,
					Yii::t('app', 'Invalid request. Please do not repeat this request again.'));
	}

	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('YumTextSettings');
		$this->render('/textsettings/index',array(
					'dataProvider'=>$dataProvider,
					));
	}

	public function actionAdmin()
	{
		$model=new YumTextSettings('search');

		$YumTextSettingsData = Yii::app()->request->getQuery('YumTextSettings');
		if($YumTextSettingsData !== null)
			$model->attributes = $YumTextSettingsData;

		$this->render('/textsettings/admin',array(
					'model'=>$model,
					));
	}

	public function loadModel()
	{
		if($this->_model===null)
		{
			$id = Yii::app()->request->getQuery('id');
			if(!empty($id))
				$this->_model = YumTextSettings::model()->findbyPk($id);

			if($this->_model===null)
				throw new CHttpException(404, Yii::t('app', 'The requested page does not exist.'));
		}
		return $this->_model;
	}

	protected function performAjaxValidation($model)
	{
		$ajax = Yii::app()->request->getPost('ajax'); 
		if($ajax == 'yum-text-settings-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
