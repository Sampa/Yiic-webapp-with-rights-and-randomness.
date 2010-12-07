<?php
Yii::import('zii.widgets.CPortlet');
Yii::import('application.modules.user.UserModule');

class MessageWidget extends CPortlet
{
	public function init()
	{
		$this->title=Yum::t('Messages');
		parent::init();
	}

	protected function renderContent()
	{
		if(!Yii::app()->user->isGuest)
		$this->render('messages', array(
					'messages' => YumMessage::model()->all()->findall()));
	}
} 
?>
