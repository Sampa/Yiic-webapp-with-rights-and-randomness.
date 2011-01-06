<?php
Yii::import('zii.widgets.CPortlet');
Yii::import('application.modules.user.UserModule');

class ProfileVisitWidget extends CPortlet
{
	public function init()
	{
		$this->title=Yum::t('Profile visits');
		parent::init();
	}

	protected function renderContent()
	{
		if(!Yii::app()->user->isGuest)
		$this->render('profile_visits', array(
					'visits' => Yii::app()->user->data()->visits));
	}
} 
?>
