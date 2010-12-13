<?php

class YumProfileCommentController extends YumController
{
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
					'actions'=>array('index'),
					'users'=>array('*'),
					),
				array('allow', 
					'actions'=>array('create'),
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

	public function actionCreate()
	{
		$model = new YumProfileComment;

		if(isset($_POST['YumProfileComment'])) {
			$model->attributes = $_POST['YumProfileComment'];

			if($model->save()) {
				$this->renderPartial('/profileComment/success');
				Yii::app()->end();
			}
		}

		$this->renderPartial('/profileComment/create',array(
					'model'=>$model,
					'profile' => YumProfile::model()->findByPk($_POST['YumProfileComment']['profile_id'])
					));
	}


	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			$this->loadModel()->delete();

			if(!isset($_GET['ajax']))
			{
				if(isset($_POST['returnUrl']))
					$this->redirect($_POST['returnUrl']); 
				else
					$this->redirect(array('admin'));
			}
		}
		else
			throw new CHttpException(400,
					Yii::t('app', 'Invalid request. Please do not repeat this request again.'));
	}

	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('ProfileComment');
		$this->render('index',array(
					'dataProvider'=>$dataProvider,
					));
	}

	public function actionAdmin()
	{
		$model=new ProfileComment('search');
		$model->unsetAttributes();

		if(isset($_GET['ProfileComment']))
			$model->attributes = $_GET['ProfileComment'];

		$this->render('admin',array(
					'model'=>$model,
					));
	}

}
