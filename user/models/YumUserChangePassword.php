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

		$passwordrule = array_merge(array('password', 'YumPasswordValidator'), 
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
	
	
	public function createRandomPassword($lowercase=0,$uppercase=0,$numbers=0) {
	$max=$lowercase + $uppercase + $numbers;
    $chars = "abcdefghijkmnopqrstuvwxyz";
    $numbers = "1023456789";
    srand((double)microtime()*1000000);
    $i = 0;
    $current_lc=0;
    $current_uc=0;
    $current_dd=0;
    $password = '' ;
    while ($i <= $max) {
		if ($current_lc < $lowercase)
		{
        $charnum = rand() % 22;
        $tmpchar = substr($chars, $charnum, 1);
        $password = $password . $tmpchar;
        $i++;
	    }
	    
	    if ($current_uc < $uppercase)
		{
        $charnum = rand() % 22;
        $tmpchar = substr($chars, $charnum, 1);
        $password = $password . strtoupper($tmpchar);
        $i++;
	    }
	    
	     if ($current_dd < $numbers)
		{
        $charnum = rand() % 9;
        $tmpchar = substr($numbers, $charnum, 1);
        $password = $password . $tmpchar;
        $i++;
	    }
    }
    return $password;
}




} 
