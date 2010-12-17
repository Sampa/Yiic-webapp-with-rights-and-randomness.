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
					'actions'=>array('create', 'delete'),
					'users'=>array('@'),
					),
				array('allow', 
					'actions'=>array('admin'),
					'users'=>array('admin'),
					),
				array('deny', 
					'users'=>array('*'),
					),
				);
	}

	public function actionCreate() {
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


	public function actionDelete() {
		$comment = YumProfileComment::model()->findByPk($_GET['id']);
		if($comment->user_id = Yii::app()->user->id
				|| $comment->profile_id = Yii::app()->user->id) {
			$comment->delete();
			$this->redirect(array('//user/profile/view', 'id' => $comment->profile_id));
		} else
			throw new CHttpException(400,
					Yum::t('You are not the owner of this Comment or this Profile!'));
	}

	public function actionIndex() {
		$dataProvider=new CActiveDataProvider('ProfileComment');
		$this->render('index',array(
					'dataProvider'=>$dataProvider,
					));
	}

	public function actionAdmin() {
		$model=new ProfileComment('search');
		$model->unsetAttributes();

		if(isset($_GET['ProfileComment']))
			$model->attributes = $_GET['ProfileComment'];

		$this->render('admin',array(
					'model'=>$model,
					));
	}

}
