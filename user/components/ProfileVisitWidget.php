<?php
Yii::import('application.modules.user.UserModule');
Yii::import('zii.widgets.CPortlet');

class ProfileVisitWidget extends CPortlet
{
	public function init() {
		parent::init();
		if(Yii::app()->user->isGuest)
			return false;

		$this->title=Yum::t('Profile visits');
	}

	protected function renderContent()
	{
		parent::renderContent();
		if(Yii::app()->user->isGuest)
			return false;

			$this->render('profile_visits', array(
						'visits' => Yii::app()->user->data()->visits));
	}
} 
?>
