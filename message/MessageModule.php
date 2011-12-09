<?php
Yii::setPathOfAlias('MessagesModule' , dirname(__FILE__));

Yii::import('MessagesModule.models.*');

class MessageModule extends CWebModule {
	// System-wide configuration option on how users should be notified about
  // new internal messages by email. Available options:
  // None, Digest, Instant, User, Treshhold
	// 'User' means to use the user-specific option in the user table
	public $messageTable = '{{message}}';
	public $notifyType = 'user';

	public $layout = 'application.modules.user.views.layouts.yum';

	// Send a message to the user if the email changing has been succeeded
	public $notifyEmailChange = true;

	// Messaging System can be MSG_NONE, MSG_PLAIN or MSG_DIALOG
	public $messageSystem = YumMessage::MSG_DIALOG;

	public $controllerMap=array(
		'message'=>array('class'=>'MessagesModule.controllers.YumMessageController'),
	);

}
