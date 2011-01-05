<?php

class YumMembershipController extends YumController {

	public function accessRules()
	{
		return array(
				array('allow', 
					'actions'=>array('index', 'order'),
					'users'=>array('@'),
					),
				array('allow', 
					'actions'=>array('admin','delete', 'update', 'view', 'orders'),
					'users'=>array('admin'),
					),
				array('deny', 
					'users'=>array('*'),
					),
				);
	}

	public function actionView()
	{
		return false;
	}

	public function actionUpdate() {

		if(isset($_POST['YumMembership'])) {
		$model = YumMembership::model()->find(
				'membership_id = '.$_POST['YumMembership']['membership_id'] 
				.' and user_id = '.$_POST['YumMembership']['user_id']);

			$model->attributes = $_POST['YumMembership'];
			$model->payment_date = time();
			$model->end_time = $model->payment_date + $model->role->duration;

			if($model->save()) {
				$this->redirect(array('admin'));
			}
		}

		if(!isset($model))
		$model = YumMembership::model()->find(
				'membership_id = '.$_GET['id']['membership_id'] 
				.' and user_id = '.$_GET['id']['user_id']);

		$this->render('update',array(
					'model'=>$model,
					));
	}

	public function actionOrder()
	{
		$model = new YumMembership;

		if(isset($_POST['YumMembership'])) {
			$model->attributes = $_POST['YumMembership'];
			if($model->save()) {
				$this->redirect(array('index'));
			}
		}

		$this->render('order',array( 'model'=>$model));
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
		$dataProvider=new CActiveDataProvider('YumMembership', array(
					'criteria' => array(
						'condition' => 'user_id = '.Yii::app()->user->id),
					));

		$this->render('index',array(
					'dataProvider'=>$dataProvider,
					));
	}

	public function actionOrders()
	{
		$model=new YumMembership('search');
		$model->unsetAttributes();

		if(isset($_GET['YumMembership']))
			$model->attributes = $_GET['YumMembership'];

		$this->render('orders',array(
					'model'=>$model,
					));
	}

	public function actionAdmin()
	{
		$model=new YumMembership('search');
		$model->unsetAttributes();

		if(isset($_GET['YumMembership']))
			$model->attributes = $_GET['YumMembership'];

		$this->render('admin',array(
					'model'=>$model,
					));
	}

}
