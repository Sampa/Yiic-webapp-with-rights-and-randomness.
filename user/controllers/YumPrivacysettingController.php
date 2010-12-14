<?php

class YumPrivacysettingController extends YumController
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
					'actions'=>array('update'),
					'users'=>array('@'),
					),
				array('deny', 
					'users'=>array('*'),
					),
				);
	}

	public function actionUpdate() {
		$model = YumPrivacySetting::model()->findByPk(Yii::app()->user->id);
		if(isset($_POST['YumPrivacysetting'])) {
			$model->attributes = $_POST['YumPrivacysetting'];
			if($model->save()) {
				$this->redirect(array('//user/profile/view'));
			}
		}

		if(!$model) {
			$model = new YumPrivacySetting();
			$model->user_id = Yii::app()->user->id;
			$model->save();
			$this->refresh();
		}

		$this->render('update',array(
					'model'=>$model,
					));
	}

}
