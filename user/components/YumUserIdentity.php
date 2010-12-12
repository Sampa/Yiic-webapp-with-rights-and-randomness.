<?php

class YumUserIdentity extends CUserIdentity {
	private $id;
	public $user;
	public $facebook_id=null;
	public $facebook_user=null;
	const ERROR_EMAIL_INVALID=3;
	const ERROR_STATUS_NOTACTIVE=4;
	const ERROR_STATUS_BANNED=5;

	public function authenticateFacebook() {
		$fbconfig = Yum::module()->facebookConfig;
		if (!$fbconfig || $fbconfig && !is_array($fbconfig))
			throw new Exception('actionLogout for Facebook was called, but is not activated in application configuration.php');

		Yii::import('application.modules.user.vendors.facebook.*');
		require_once('Facebook.php');
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

	public function authenticate() {
		$user = YumUser::model()->find('username = :username', array(
					':username' => $this->username));

		if(YumUser::encrypt($this->password)!==$user->password)
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		else if($user->status == 0)
			$this->errorCode=self::ERROR_STATUS_NOTACTIVE;
		else if($user->status == -1)
			$this->errorCode=self::ERROR_STATUS_BANNED;
		else {
			$this->id = $user->id;
			$this->setState('id', $user->id);
			$this->username = $user->username;
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

	public function getRoles()
	{
		return $this->Role;
	}

}
