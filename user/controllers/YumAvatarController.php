<?php

// This controller handles the upload and the deletion of an Avatar
// image for the user profile.

class YumAvatarController extends YumController {
	public function actionRemoveAvatar() {
		$model = YumUser::model()->findByPk(Yii::app()->user->id);
		$model->avatar = '';
		$model->save();
		$this->redirect(array('user/profile'));	
	}

	public function beforeAction($action) {
		// Disallow guests
		if(Yii::app()->user->isGuest)
			$this->redirect(Yum::module()->loginUrl);

		// Stop action if Avatars are disabled in the module configuration
		if(!Yum::module()->enableAvatar)
			return false;

		return parent::beforeAction($action);
	}

	public function actionEditAvatar() {
		$model = YumUser::model()->findByPk(Yii::app()->user->id);

		if(isset($_POST['YumUser'])) {
			$model->attributes = $_POST['YumUser'];
			$model->setScenario('avatarUpload');

			if(Yum::module()->avatarMaxWidth != 0)
				$model->setScenario('avatarSizeCheck');

			$model->avatar = CUploadedFile::getInstanceByName('YumUser[avatar]');
			if($model->validate()) {
				if ($model->avatar instanceof CUploadedFile) {

					// Prepend the id of the user to avoid filename conflicts
					$filename = Yum::module()->avatarPath .'/'.  $model->id . '_' . $_FILES['YumUser']['name']['avatar'];
					$model->avatar->saveAs($filename);
					$model->avatar = $filename;
					if($model->save()) {
						Yum::setFlash(Yum::t('The image was uploaded successfully'));
						Yum::logActivity(Yii::app()->user->id, 'avatar_uploaded');
						$this->redirect(array('user/profile'));	
					}
				}
			}
		}

		$this->render('edit_avatar', array('model' => $model));
	}
}
