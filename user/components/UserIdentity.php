<?php

Yii::import('application.modules.user.models.*');

class UserIdentity extends CUserIdentity
{
	private $id;
	const ERROR_EMAIL_INVALID=3;
	const ERROR_STATUS_NOTACTIV=4;
	const ERROR_STATUS_BAN=5;

	public function authenticate()
	{
		if (strpos($this->username,"@")) {
			$user=User::model()->findByAttributes(array('email'=>$this->username));
		} else {
			$user=User::model()->findByAttributes(array('username'=>$this->username));
		}
		if($user===null)
			if (strpos($this->username,"@")) {
				$this->errorCode=self::ERROR_EMAIL_INVALID;
			} else {
				$this->errorCode=self::ERROR_USERNAME_INVALID;
			}
		else if(User::encrypting($this->password)!==$user->password)
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		else if($user->status==0&&Yii::app()->User->loginNotActiv==false)
			$this->errorCode=self::ERROR_STATUS_NOTACTIV;
		else if($user->status==-1)
			$this->errorCode=self::ERROR_STATUS_BAN;
		else {
			$this->id=$user->id;
			$this->setState('id', $user->id);
			$this->username=$user->username;
			$this->errorCode=self::ERROR_NONE;
		}
		return !$this->errorCode;
	}
    
    /**
    * @return integer the ID of the user record
    */
	public function getId()
	{
		return $this->id;
	}

	public function getRoles() {
		return $this->Role;
	}

}
