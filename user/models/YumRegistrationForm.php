<?php
/**
 * RegistrationForm class.
 * RegistrationForm is the data structure for keeping
 * user registration form data. It is used by the 'registration' action of 'YumUserController'.
 * @package Yum.models
 */
class YumRegistrationForm extends YumUser {
	public $verifyPassword;
	
	/**
	 * @var string
	 */
	public $verifyCode;

	public function rules() 
	{
		$rules = parent::rules();
		if(Yii::app()->getModule('user')->loginType != 'LOGIN_BY_EMAIL' || Yum::module()->registrationType ==REG_NO_USERNAME_OR_PASSWORD || Yum::module()->registrationType == REG_NO_USERNAME_OR_PASSWORD_ADMIN)
			$rules[] = array('username', 'required');
		$rules[] = array('password, verifyPassword', 'required');
		$rules[] = array('password', 'compare', 'compareAttribute'=>'verifyPassword', 'message' => Yii::t("UserModule.user", "Retype password is incorrect."));
		$rules[] = array('verifyCode', 'captcha', 'allowEmpty'=>!extension_loaded('gd')||!Yii::app()->getModule('user')->enableCaptcha);

		return $rules;


	}
	
	public function genRandomString( $length = 10)
	{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $string ='';    
    for ($p = 0; $p < $length; $p++)
     {
        $string .= $characters[mt_rand(0, strlen($characters))];
    }
    return $string;
	}

}
