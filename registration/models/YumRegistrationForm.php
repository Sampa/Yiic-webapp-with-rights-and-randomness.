<?php
/**
 * RegistrationForm class.
 * RegistrationForm is the data structure for keeping
 * user registration form data. It is used by the 'registration' action of 
 * 'YumRegistrationController'.
 * @package Yum.models
 */
class YumRegistrationForm extends YumUser {
	public $username;
	public $password;
	public $verifyPassword;
	public $verifyCode; // Captcha

	public function rules() 
	{
		$rules = parent::rules();

		$rules[] = array('username', 'required');
		$rules[] = array('password, verifyPassword', 'required');
		$rules[] = array('password', 'compare',
				'compareAttribute'=>'verifyPassword',
				'message' => Yum::t("Retype password is incorrect."));
		if(Yum::module('registration')->enableCaptcha)
			$rules[] = array('verifyCode', 'captcha',
					'allowEmpty'=>CCaptcha::checkRequirements()); 

		return $rules;
	}
}
