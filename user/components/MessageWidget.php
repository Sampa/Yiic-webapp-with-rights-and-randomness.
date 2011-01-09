<?php
Yii::import('application.modules.user.UserModule');
Yii::import('zii.widgets.CPortlet');

class MessageWidget extends CPortlet
{
	public function init()
	{
		$this->title=Yum::t('New messages');
		parent::init();
	}

	protected function renderContent()
	{
		$messages = YumMessage::model()->unread()->findAll();

		if(!Yii::app()->user->isGuest)
			$this->render('messages', array(
						'messages' => $messages
						));
	}
} 
?>
