<?php

class YumAuthController extends YumController {
	public $defaultAction = 'login';
	public $loginForm = null;
	private $_model;

	public function accessRules() {
		return array(
				array('allow',
					'actions'=>array('login', 'facebook'),
					'users'=>array('*'),
					),
				array('allow',
					'actions'=>array('logout'),
					'users'=>array('@'),
					),
				array('deny',  // deny all other users
					'users'=>array('*'),
					),
				);
	}

	public function loginByFacebook() {
		if (!Yum::module()->loginType & UserModule::LOGIN_BY_FACEBOOK)
			throw new Exception('actionFacebook was called, but is not activated in application configuration');

		Yii::app()->user->logout();
		Yii::import('application.modules.user.vendors.facebook.*');
		$facebook = new Facebook(Yum::module()->facebookConfig);
		$fb_uid = $facebook->getUser();
		if ($fb_uid) {
			$profile = YumProfile::model()->findByAttributes(array('facebook_id' => $fb_uid));
			$user = ($profile) ? YumUser::model()->findByPk($profile->user_id) : null;
			try
			{
				$fb_user = $facebook->api('/me');
				if ($user === null)
				{
					$user = new YumUser;
					$user->username = 'fb_'.YumRegistrationForm::genRandomString(Yum::module()->usernameRequirements['maxLen'] - 3);
					$user->password = YumUserChangePassword::createRandomPassword(Yum::module()->passwordRequirements['minLowerCase'],Yum::module()->passwordRequirements['minUpperCase'],Yum::module()->passwordRequirements['minDigits'],Yum::module()->passwordRequirements['minLen']);
					$user->activationKey = YumUser::encrypt(microtime().$user->password);
					$user->createtime = time();
					$user->lastvisit = time();
					$user->status = YumUser::STATUS_ACTIVE;
					$user->superuser = 0;
					if ($user->save())
					{
						$profile = new YumProfile;
						$profile->user_id = $user->id;
						$profile->facebook_id = $fb_user['id'];
						$profile->email = $fb_user['email'];
						$profile->save(false);
					}
				}

				$identity = new YumUserIdentity($fb_uid, $user->id);
				$identity->authenticateFacebook(true);

				switch ($identity->errorCode)
				{
					case YumUserIdentity::ERROR_NONE:
						$duration = 3600*24*30; //30 days
						Yii::app()->user->login($identity, $duration);
						if(Yum::module()->enableLogging == true)
							YumActivityController::logActivity($user, 'fblogin');
						$this->redirect(Yum::module()->returnUrl);
						break;
					case YumUserIdentity::ERROR_STATUS_NOTACTIVE:
						$user->addError('status', Yum::t('Your account is not activated.'));
						break;
					case YumUserIdentity::ERROR_STATUS_BANNED:
						$user->addError('status', Yum::t('Your account is blocked.'));
						break;
					case YumUserIdentity::ERROR_PASSWORD_INVALID:
						$user->addError('status', Yum::t('Password incorrect.'));
						break;
				}
			}
			catch (FacebookApiException $e)
			{
				/* FIXME: Workaround for avoiding the 'Error validating access token.'
				 * inmediatly after a user logs out. This is nasty. Any other
				 * approach to solve this issue is more than welcomed.
				 */

				if(Yum::module()->enableLogging == true)
					YumActivityController::logActivity($user, 'fb_failed_login_attempt');
				$this->redirect($this->createUrl(Yum::module()->returnLogoutUrl));
			}
		}
		else
			$this->redirect(Yum::module()->returnLogoutUrl);
	}


	public function loginByUsername() {
		if($user = YumUser::model()->find('username = :username', array(
						':username' => $this->loginForm->username))) 
			return $this->authenticate($user);
		else
			$this->loginForm->addError("password",
					Yum::t('Username or Password is incorrect'));

		return false;
	}

	public function authenticate($user) {

		$identity = new YumUserIdentity($user->username, $this->loginForm->password);
		$identity->authenticate();
			switch($identity->errorCode) {
				case YumUserIdentity::ERROR_NONE:
					$duration = $this->loginForm->rememberMe ? 3600*24*30 : 0; // 30 days
					Yii::app()->user->login($identity,$duration);
					return $user;
					break;
				case YumUserIdentity::ERROR_EMAIL_INVALID:
					$this->loginForm->addError("password",Yum::t('Username or Password is incorrect'));
					break;
				case YumUserIdentity::ERROR_STATUS_NOTACTIVE:
					$this->loginForm->addError("status",Yum::t('This account is not activated.'));
					break;
				case YumUserIdentity::ERROR_STATUS_BANNED:
					$this->loginForm->addError("status",Yum::t('This account is blocked.'));
					break;
				case YumUserIdentity::ERROR_PASSWORD_INVALID:
					if(!$this->loginForm->hasErrors())
						$this->loginForm->addError("password",Yum::t('Username or Password is incorrect'));
					break;
				return false;
	}
 }

	public function loginByEmail() {
		$profile = YumProfile::model()->find('email = :email', array(
					':email' => $this->loginForm->username));
		if($profile && $profile->user) 
			return $this->authenticate($profile->user);

		return false;
	}

	public function loginByOpenid() {
	}

	public function loginByTwitter() {
	}

	public function actionLogin() {
		// If the user is already logged in send them to the users logged homepage
		if (!Yii::app()->user->isGuest)
			$this->redirect(Yum::module()->returnUrl);

		$this->layout = Yum::module()->loginLayout;
		$this->loginForm = new YumUserLogin;

		if(isset($_POST['YumUserLogin'])) {
			$this->loginForm->attributes = $_POST['YumUserLogin'];

			// validate user input and try all activated login methods
			if($this->loginForm->validate()) {
				$success = false;
				if(Yum::module()->loginType & UserModule::LOGIN_BY_USERNAME)
					$success = $this->loginByUsername();
				if(Yum::module()->loginType & UserModule::LOGIN_BY_EMAIL && !$success)
					$success = $this->loginByEmail();
				if(Yum::module()->loginType & UserModule::LOGIN_BY_FACEBOOK && !$success)
			 		$success = $this->loginByFacebook();
				if(Yum::module()->loginType & UserModule::LOGIN_BY_OPENID && !$success)
					$success = $this->loginByOpenid();
				if(Yum::module()->loginType & UserModule::LOGIN_BY_TWITTER && !$success)
					$sucess = $this->loginByTwitter();

				if($success instanceof YumUser) {
					$success->lastvisit = time();
					$success->save();
					$this->redirectUser($success);
				}
			} 
		}

		$this->render(Yum::module()->loginView, array(
					'model' => $this->loginForm));
	}

	public function redirectUser($user) {
		if($user->superuser) {
			$this->redirect(Yum::module()->returnAdminUrl);
		} else {
			if ($user->isPasswordExpired())
				$this->redirect(array('passwordexpired'));
			else if(Yum::module()->returnUrl !== '')
				$this->redirect(Yum::module()->returnUrl);
			else
				$this->redirect(Yii::app()->user->returnUrl);
		}

	}

	public function actionLogout()
	{
		// If the user is already logged out send them to returnLogoutUrl
		if (Yii::app()->user->isGuest)
			$this->redirect(Yum::module()->returnLogoutUrl);

		$user = YumUser::model()->findByPk(Yii::app()->user->id);

		if (Yii::app()->user->name == 'facebook')
		{
			$fbconfig = Yum::module()->facebook;
			if (!$fbconfig || $fbconfig && !is_array($fbconfig))
				throw new Exception('actionLogout for Facebook was called, but is not activated in main.php');

			Yii::import('application.vendors.facebook.*');
			require_once('facebook.php');
			$facebook = new Facebook($fbconfig);
			$session = $facebook->getSession();
			Yii::app()->user->logout();
			if(Yum::module()->enableLogging == true)
				YumActivityController::logActivity(Yii::app()->user->id, 'fblogout');
			$this->redirect($facebook->getLogoutUrl(array('next'=>$this->createAbsoluteUrl(Yum::module()->returnLogoutUrl), 'session_key'=>$session['session_key'])));

		}
		else
		{
			if(Yum::module()->enableLogging == true)
				YumActivityController::logActivity(Yii::app()->user->id, 'logout');

			Yii::app()->user->logout();
			$this->redirect(Yum::module()->returnLogoutUrl);
		}
	}

}
