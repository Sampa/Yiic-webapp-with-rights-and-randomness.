<?php

class YumRoleController extends YumController
{
	private $_model;

	public function actionView()
	{
		$this->layout = Yum::module()->adminLayout;
		$model = $this->loadModel();
		$this->render('view',array('model'=>$model));
	}

	public function actionCreate() 
	{
		$this->layout = Yum::module()->adminLayout;
		$model = new YumRole();
		$this->performAjaxValidation($model);
		if(isset($_POST['YumRole'])) {
			$model->attributes = $_POST['YumRole'];

			if(isset($_POST['YumRole']['YumUser']))
				$model->users = $_POST['YumRole']['YumUser'];
			else
				$model->users = array();

			if($model->save())
			{
			if(Yum::module()->enableLogging == true)
								{
								$user= YumUser::model()->findbyPK(Yii::app()->user->id);
								YumActivityController::logActivity($user, 'role_created');
								}
				$this->redirect(array('admin'));
			}

		}
		$this->render('create', array('model' => $model));
	}

	public function actionUpdate()
	{
		$this->layout = Yum::module()->adminLayout;
		$model = $this->loadModel();

	 $this->performAjaxValidation($model);

		if(isset($_POST['YumRole'])) {
			$model->attributes = $_POST['YumRole'];

			if(isset($_POST['YumRole']['YumUser']))
				$model->users = $_POST['YumRole']['YumUser'];
			else
				$model->users = array();

			if($model->validate() && $model->save())
			{
				if(Yum::module()->enableLogging == true)
								{
								$user= YumUser::model()->findbyPK(Yii::app()->user->id);
								YumActivityController::logActivity($user, 'role_updated');
								}
				$this->redirect(array('view','id'=>$model->id));
		}
	}

		$this->render('update',array(
			'model'=>$model,
		));
	}

	public function actionAdmin() 
	{
		$this->layout = Yum::module()->adminLayout;
		$dataProvider=new CActiveDataProvider('YumRole', array(
			'pagination'=>array(
				'pageSize'=>20,
			),
		));

		$this->render('admin',array(
			'dataProvider'=>$dataProvider,
		));

	}

	public function actionDelete()
	{
		$this->layout = Yum::module()->adminLayout;	
		if(Yii::app()->request->isPostRequest)
		{
			if(Yum::module()->enableLogging == true)
			{
				$user= YumUser::model()->findbyPK(Yii::app()->user->id);
				YumActivityController::logActivity($user, 'role_removed');
			}
			$this->loadModel()->delete();

			if(!isset($_POST['ajax']))
				$this->redirect(array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionIndex()
	{
		$this->actionAdmin();
	}


	/**
	 * @return YumRole
	 */
	public function loadModel()
	{
		if($this->_model===null)
		{
			if(isset($_GET['id']))
				$this->_model=YumRole::model()->findbyPk($_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,Yii::t('App', 'The requested page does not exist.'));
		}
		return $this->_model;
	}

	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='yum-role-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
