<?php

class YumAvatarController extends YumController {
	// Only allow the current logged in user to remove his Avatar
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

			$model->avatar = CUploadedFile::getInstanceByName('YumUser[avatar]');
			if($model->validate()) {
				if(Yum::module()->avatarScaleImage) {
					Yii::import('YumModule.vendors.imagemodifier.*');

					$model->setScenario('avatarScale');
					$modifier = new CImageModifier;
					$img = $modifier->load($_FILES['YumUser']);
					if ($img->uploaded) {
						$img->image_resize = true;
						$img->image_ratio_y = true;
						$img->image_x = Yum::module()->avatarMaxWidth;

						foreach(Yum::module()->imageModifierOptions as $key => $option)
							$img->{$key} = $option;
						$img->file_dst_name = $model->id . '_' . $_FILES['YumUser']['name']['avatar'];
						$img->process(Yum::module()->avatarPath);
						if ($img->processed) {
							Yum::setFlash(
									Yum::t('The image has been resized to {max_pixel}px width successfully', array(
											'{max_pixel}' => Yum::module()->avatarMaxWidth)));
							$img->clean();
							$this->redirect(array('user/profile'));	
						} else {
							Yum::setFlash(
									Yum::t('Error while processing new avatar image : {error_message}; File was uploaded without resizing', array(
											'{error_message}' => $img->error)));
						}
						Yum::logActivity(Yii::app()->user->id, 'avatar_uploaded', $img->log);
					} else {
						Yum::setFlash('An error occured while uploading your avatar image: ' . $img->error);
					}
				} else {
					if(Yum::module()->avatarMaxWidth != 0)
						$model->setScenario('avatarSizeCheck');

					if ($model->avatar instanceof CUploadedFile) {
						$filename = Yum::module()->avatarPath .'/'.  $model->id . '_' . $_FILES['YumUser']['name']['avatar'];
						$model->avatar->saveAs($filename);
						$model->avatar = $filename;
					}
				}
				if($model->save())
					$this->redirect(array('user/profile'));	
			}
		}

		$this->render('edit_avatar', array('model' => $model));
	}

}
