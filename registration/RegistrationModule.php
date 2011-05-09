<?php
Yii::setPathOfAlias('RegistrationModule' , dirname(__FILE__));

class RegistrationModule extends CWebModule {
	public $enableRecovery = true;

	public $registrationUrl = array('//registration/registration/registration');
	public $recoveryUrl = array('//registration/registration/recovery');

	// Whether to confirm the activation of an user by email
	public $enableActivationConfirmation = true; 

	public $registrationEmail='register@website.com';
	public $recoveryEmail='restore@website.com';

	/**
	 * Whether to use captcha in registration process
	 * @var boolean
	 */
	public $enableCaptcha = true;

	public $controllerMap=array(
			'registration'=>array(
				'class'=>'RegistrationModule.controllers.YumRegistrationController'),
			);

}
