<?php
/**
 * RegistrationForm class.
 * RegistrationForm is the data structure for keeping
 * user registration form data. It is used by the 'registration' action of 'YumUserController'.
 * @package Yum.models
 */
class YumRegistrationForm extends YumUser {
	/**
	 * @var string
	 */
	public $verifyPassword;
	
	/**
	 * @var string
	 */
	public $verifyCode;

	public function rules() 
	{
		$rules = parent::rules();
		if(Yii::app()->getModule('user')->loginType != 'LOGIN_BY_EMAIL')
			$rules[] = array('username', 'required');
		$rules[] = array('password, verifyPassword', 'required');
		$rules[] = array('password', 'compare', 'compareAttribute'=>'verifyPassword', 'message' => Yii::t("UserModule.user", "Retype password is incorrect."));
		$rules[] = array('verifyCode', 'captcha', 'allowEmpty'=>!extension_loaded('gd')||!Yii::app()->getModule('user')->enableCaptcha);

		return $rules;


	}

}
