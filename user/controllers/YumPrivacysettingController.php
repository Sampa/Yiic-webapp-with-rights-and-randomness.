<?php

class YumPrivacysettingController extends YumController
{
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

			$profile_privacy = 0;
			foreach($_POST as $key => $value) {
				if($value == 1 && substr($key, 0, 18) == 'privacy_for_field_') {
					$data = explode('_', $key);
					$data = (int) $data[3];
					$profile_privacy += $data;
				}
			}

			$model->public_profile_fields = $profile_privacy;
			$model->validate();

		if(isset($_POST['YumProfile'])) {
			$profile = $model->user->profile;
			$profile->attributes = $_POST['YumProfile'];
			$profile->validate();
			}

			if(!$model->hasErrors() && !$model->user->profile->hasErrors()) {
				$profile->save();
				$model->save();
				Yum::setFlash('Your privacy settings have been saved');
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
					'profile'=> isset($model->user) && isset($model->user->profile)
					? $model->user->profile 
					: null
					));
	}

}
