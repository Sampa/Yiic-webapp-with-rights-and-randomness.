<?php

class YumAuthController extends YumController {
	public $defaultAction = 'login';
	public $loginForm = null;

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

	public function loginByUsername() {
		if(Yum::module()->caseSensitiveUsers)
			$user = YumUser::model()->find('username = :username', array(
						':username' => $this->loginForm->username));

		if($user)
			return $this->authenticate($user);
		else
			$this->loginForm->addError('password',
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
			case YumUserIdentity::ERROR_STATUS_REMOVED:
				$this->loginForm->addError('status', Yum::t('Your account has been deleted.'));
				break;

			case YumUserIdentity::ERROR_PASSWORD_INVALID:
				Yum::log(Yum::t('Failed login attempt for {username}', array(
								'{username}' => $this->loginForm->username)), 'error');

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
		if (!Yum::module()->loginType & UserModule::LOGIN_BY_OPENID)
			throw new Exception('login by Open Id was called, but is not activated in application configuration');

		Yii::app()->user->logout();
		Yii::import('application.modules.user.vendors.openid.*');
		$openid = new EOpenID;
		$openid->authenticate($this->loginForm->username);
		return Yii::app()->user->login($openid);
	}

	public function loginByTwitter() {
		return false;
	}

	public function actionLogin() {
		// If the user is already logged in send them to the users logged homepage
		if (!Yii::app()->user->isGuest)
			$this->redirect(Yum::module()->returnUrl);

		$this->loginForm = new YumUser;

		$success = false;
		$action = 'login';
		$login_type = null;
		if (isset($_POST['YumUserLogin'])) {
			$this->loginForm->attributes = $_POST['YumUserLogin'];
			// validate user input for the rest of login methods
			if ($this->loginForm->validate()) {
				if (Yum::module()->loginType & UserModule::LOGIN_BY_USERNAME) {
					$success = $this->loginByUsername();
					if ($success)
						$login_type = 'username';
				}
				if (Yum::module()->loginType & UserModule::LOGIN_BY_EMAIL && !$success) {
					$success = $this->loginByEmail();
					if ($success)
						$login_type = 'email';
				}
				if ($success instanceof YumUser) {
					//cookie with login type for later flow control in app
					if ($login_type) {
						$cookie = new CHttpCookie('login_type', serialize($login_type));
						$cookie->expire = time() + (3600*24*30);
						Yii::app()->request->cookies['login_type'] = $cookie;
					}
					Yum::log(Yum::t('User {username} successfully logged in', array(
									'{username}' => $success->username)));
					$this->redirectUser($success);
				}

				$this->render(Yum::module()->loginView, array(
							'model' => $this->loginForm));
			}
		}
	}

			public function redirectUser($user) {
				if ($user->superuser) {
					$this->redirect(Yum::module()->returnAdminUrl);
				} else {
					if ($user->isPasswordExpired())
						$this->redirect(array('passwordexpired'));
					else if($user->lastvisit == 0) {
						$user->lastvisit = time();
						$user->save(true, array('lastvisit'));
						$this->redirect(Yum::module()->firstVisitUrl);
					}
					else if (Yum::module()->returnUrl !== '')
						$this->redirect(Yum::module()->returnUrl);
					else
						$this->redirect(Yii::app()->user->returnUrl);
				}
			}

			public function actionLogout() {
				// If the user is already logged out send them to returnLogoutUrl
				if (Yii::app()->user->isGuest)
					$this->redirect(Yum::module()->returnLogoutUrl);

				//let's delete the login_type cookie
				$cookie=Yii::app()->request->cookies['login_type'];
				if ($cookie) {
					$cookie->expire = time() - (3600*72);
					Yii::app()->request->cookies['login_type'] = $cookie;
				}

				if($user = YumUser::model()->findByPk(Yii::app()->user->id)) {
					$username = $user->username;
					$user->logout();

					if (Yii::app()->user->name == 'facebook') {
						if (!Yum::module()->loginType & UserModule::LOGIN_BY_FACEBOOK)
							throw new Exception('actionLogout for Facebook was called, but is not activated in main.php');

						Yii::import('application.modules.user.vendors.facebook.*');
						require_once('Facebook.php');
						$facebook = new Facebook(Yum::module()->facebookConfig);
						$fb_cookie = 'fbs_'.Yum::module()->facebookConfig['appId'];
						$cookie = Yii::app()->request->cookies[$fb_cookie];
						if ($cookie) {
							$cookie->expire = time() -1*(3600*72);
							Yii::app()->request->cookies[$cookie->name] = $cookie;
						}
						$session = $facebook->getSession();
						Yum::log('Facebook logout from user '. $username);
						Yii::app()->user->logout();
						$this->redirect($facebook->getLogoutUrl(array('next' => $this->createAbsoluteUrl(Yum::module()->returnLogoutUrl), 'session_key' => $session['session_key'])));
					}
					else {
						Yum::log(Yum::t('User {username} logged off', array(
										'{username}' => $username)));

						Yii::app()->user->logout();
					}
				}
				$this->redirect(Yum::module()->returnLogoutUrl);
			}
		}
