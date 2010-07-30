<?php
/**
 * UserChangePassword class.
 * UserChangePassword is the data structure for keeping
 * user change password form data. It is used by the 'changepassword' action 
 * of 'UserController'.
 */

class YumUserChangePassword extends YumFormModel 
{
	public $password;
	public $verifyPassword;

	public function rules() 
	{
		$passwordRequirements = Yii::app()->getModule('user')->passwordRequirements;

		$passwordrule = array_merge(array('password', 'CPasswordValidator'), 
				$passwordRequirements);

		$rules[] = $passwordrule;
		$rules[] = array('password, verifyPassword', 'required');
		$rules[] = array('password', 'compare', 'compareAttribute'=>'verifyPassword',
				'message' => Yii::t("UserModule.user", "Retype password is incorrect."));

		return $rules; 
	}

	/**
	 * Declares attribute labels.
	 */
	public function attributeLabels()
	{
		return array(
			'password'=>Yii::t("UserModule.user", "password"),
			'verifyPassword'=>Yii::t("UserModule.user", "Retype password"),
		);
	}
} 
