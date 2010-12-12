<?php

class YumUserIdentity extends CUserIdentity
{
	private $id;
	public $facebook_id=null;
	public $facebook_user=null;
	const ERROR_EMAIL_INVALID=3;
	const ERROR_STATUS_NOTACTIVE=4;
	const ERROR_STATUS_BANNED=5;

	public function authenticate($facebook=false)
	{
		if($facebook)
		{
			$fbconfig = Yum::module()->facebook;
			if (!$fbconfig || $fbconfig && !is_array($fbconfig))
				throw new Exception('actionLogout for Facebook was called, but is not activated in main.php');

			Yii::import('application.vendors.facebook.*');
			require_once('facebook.php');
			$facebook = new Facebook($fbconfig);

			$fb_uid = $facebook->getUser();
			$profile = YumProfile::model()->findByAttributes(array('facebook_id'=>$fb_uid));
			$user = YumUser::model()->findByPk($profile->user_id);

			if ($user === null)
				$this->errorCode = self::ERROR_USERNAME_INVALID;
			elseif($user->status == YumUser::STATUS_NOTACTIVE && !Yum::module()->loginNotActive)
				$this->errorCode = self::ERROR_STATUS_NOTACTIVE;
			elseif($user->status == YumUser::STATUS_BANNED)
				$this->errorCode = self::ERROR_STATUS_BANNED;
			else
			{
				$this->id = $user->id;
				$this->username = 'facebook';
				$this->facebook_id = $fb_uid;
				//$this->facebook_user = $facebook->api('/me');
				$this->errorCode = self::ERROR_NONE;
			}
		}
		else
		{
			$user=null;
			$loginType = Yum::module()->loginType;
			switch($loginType)
			{
				case 'LOGIN_BY_USERNAME':
					$user = YumUser::model()->findByAttributes(array('username'=>$this->username));
					break;
				case 'LOGIN_BY_EMAIL':
					$profile = YumProfile::model()->findByAttributes(array('email'=>$this->username));
					if($profile instanceof YumProfile)
						$user = $profile->user;
					break;
				case 'LOGIN_BY_USERNAME_OR_EMAIL':
					$user=YumUser::model()->findByAttributes(array('username'=>$this->username));
					if(!is_object($user))
						if(($profile=YumProfile::model()->findByAttributes(array('email'=>$this->username))) instanceof YumProfile)
							$user=$profile->user;
					break;
				default:
			}

			if($user===null)
				if ($loginType == 'LOGIN_BY_EMAIL')
				{
					$this->errorCode=self::ERROR_EMAIL_INVALID;
				}
				else
				{
					$this->errorCode=self::ERROR_USERNAME_INVALID;
				}
			else if(YumUser::encrypt($this->password)!==$user->password)
				$this->errorCode=self::ERROR_PASSWORD_INVALID;
			else if($user->status == 0)
				$this->errorCode=self::ERROR_STATUS_NOTACTIVE;
			else if($user->status==-1)
				$this->errorCode=self::ERROR_STATUS_BANNED;
			else
			{
				$this->id=$user->id;
				$this->setState('id', $user->id);
				$this->username=$user->username;
				$this->errorCode=self::ERROR_NONE;
			}
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

	public function getRoles()
	{
		return $this->Role;
	}

}
