<?php

/* This file handles a example registration process logic and some of the
 * most used functions for Registration and Activation. It is recommended to
 * extend from this class and implement your own, project-specific 
 * Registration process. If this example does exactly what you want in your
 * Project, then you can feel lucky already! */

Yii::import('application.modules.user.controllers.YumController');

class YumRegistrationController extends YumController {
	public $defaultAction = 'registration';

	// Only allow the registration if the user isn´ t already logged in and
	// the function is activated in the Module Configuration
	public function beforeAction($action) {
		if (!Yum::module()->enableRegistration || !Yii::app()->user->isGuest) 
			$this->redirect(Yii::app()->user->returnUrl);
		return parent::beforeAction($action);
	}


	public function accessRules() {
		return array(
				array('allow',
					'actions' => array('index', 'registration', 'recovery', 'activation', 'resendactivation'),
					'users' => array('*'),
					),
				array('allow',
					'actions' => array('captcha'),
					'users' => array('*'),
					),
				array('deny', // deny all other users
					'users' => array('*'),
					),
				);
	}

	public function actions() {
		return array(
				'captcha' => array(
					'class' => 'CCaptchaAction',
					'backColor' => 0xFFFFFF,
					),
				);
	}


	/*
	 * an Example implementation of an registration of an new User in the system.
	 * 
	 */
	public function actionRegistration() {
		$loginType = Yum::module()->loginType;
		$usernameRequirements = Yum::module()->usernameRequirements;

		$form = new YumRegistrationForm;
		$profile = new YumProfile;

		if (isset($_POST['YumUser']) && isset($_POST['YumProfile'])) {
			$form->attributes = $_POST['YumUser'];
			$profile->attributes = $_POST['YumProfile'];

			$form->validate();
			$profile->validate();

			if(!$form->hasErrors() && !$profile->hasErrors()) {
				$user = new YumUser;
				if ($user->register($form->username, $form->password, $profile->email)) {
					$profile->user_id = $user->id;
					$profile->save();

					if($this->sendRegistrationEmail($user))
						Yum::setFlash('Thank you for your registration. Please check your email.');
				} 
			}
		} 

		$this->render(Yum::module()->registrationView, array(
					'form' => $form,
					'profile' => $profile,
					)
				);  
	}

		public function actionActivate($user=null, $form=null) {
			if (!isset($user) && isset($_POST['YumProfile']['email'])) {
				$email = $_POST['YumProfile']['email'];
				$profile = YumProfile::model()->findAll($condition = 'email = :email', array(':email' => $email));
				$user = $profile->user;
			} else {
				$user = new YumUser;
			}
			if (!isset($form)) {
				$form = new YumRegistrationForm;
			}

			$this->render(Yum::module()->activateView, array(
						'form' => $form,
						'user' => $user,
						'activateFromWeb' => Yum::module()->activateFromWeb,
						));
		}

		public function actionResendActivation() {

			if (isset($_POST['email'])) {
				$email = $_POST['email'];
				$registrationType = Yum::module()->registrationType;
				$password = null;
				$profile = YumProfile::model()->findAll($condition = 'email = :email', array(':email' => $email));
				$user = $profile->user;
				$user->activationKey = $user->generateActivationKey();
				if ($registrationType == YumRegistration::REG_NO_PASSWORD || $registrationType == YumRegistration::REG_NO_PASSWORD_ADMIN_CONFIRMATION) {
					$password = YumUserChangePassword::createRandomPassword();
					$user->password = YumUser::model()->encrypt($password);
					$this->sendRegistrationEmail($user);
				}
				$user->save();
			} else {
				if (!isset($user) && !isset($_POST['email']))
					$user = new YumUser;
			}
			$form = new YumRegistrationForm;
			$this->render(Yum::module()->activateView, array(
						'form' => $form,
						'user' => $user,
						)
					);
		}

		// Send the Email to the given user object. $user->email needs to be set.
		public function sendRegistrationEmail($user, $password=null) {
			if (!isset($user->profile->email)) {
				throw new CException(Yum::t('Email is not set when trying to send Registration Email'));
			}
			$registrationType = Yum::module()->registrationType;

			$activation_url = $this->createAbsoluteUrl('registration/activation', array(
						'key' => $user->activationKey,
						'email' => $user->profile->email)
					);

			$content = YumTextSettings::model()->find('language = :lang', array(
						'lang' => Yii::app()->language));
			$sent = null;

			if (is_object($content)) {
				if ($registrationType == YumRegistration::REG_NO_PASSWORD || $registrationType == YumRegistration::REG_NO_PASSWORD_ADMIN_CONFIRMATION)
					$body = strtr($content->text_email_registration . "\n\nYour Activation Key is $user->activationKey ,\n\n Your temporary password is $password,", array('{activation_url}' => $activation_url));
				else
					$body = strtr($content->text_email_registration, array(
								'{username}' => $user->username,
								'{activation_url}' => $activation_url));

				$mail = array(
						'from' => Yum::module()->registrationEmail,
						'to' => $user->profile->email,
						'subject' => strtr($content->subject_email_registration, array(
								'{username}' => $user->username)),
						'body' => $body,
						);
				$sent = YumMailer::send($mail);
			}
			else {
				throw new CException(Yum::t('The messages for your application language are not defined.'));
			}

			return $sent;
		}

		/**
		 * Activation of an user account. The Email and the Activation key send
		 * by email needs to correct in order to continue. The Status will
		 * be initially set to 1 (active - first Visit) so the administrator
		 * can see, which accounts have been activated, but not yet logged in 
		 * (more than once)
		 */
		public function actionActivation($email=null, $key=null) {
			// If already logged in, we dont activate anymore
			if (!Yii::app()->user->isGuest)
				$this->redirect(Yii::app()->user->returnUrl);

			// If everything is set properly, let the model handle the Validation
			// and do the Activation
			if ($email != null && $key != null) {
				if (YumUser::activate($email, $key) != false) 
					$this->render(Yum::module()->activationSuccessView);
				else
					$this->render(Yum::module()->activationFailureView);
			}
		}

		/**
		 * Password recovery routine. The User will receive an email with an
		 * activation link. If clicked, he will be prompted to enter his new
		 * password.
		 */
		public function actionRecovery($email = null, $key = null) {
			$form = new YumUserRecoveryForm;

			if ($email != null && $key != null) {
				$profile = YumProfile::model()->findByAttributes(array('email' => $_GET['email']));
				if ($profile !== null) {
					$user = $profile->user;
					if ($user->activationKey != $key)
						$this->render(Yum::module()->recoveryView, array('form' => new YumUserRecoveryForm));
				} else {
					$this->render(Yum::module()->recoveryView, array('form' => new YumUserRecoveryForm));
				}

				$registrationType = Yum::module()->registrationType;
				$passwordform = new YumUserChangePassword;

				if ($registrationType == YumRegistration::REG_NO_PASSWORD
						|| $registrationType == YumRegistration::REG_NO_PASSWORD_ADMIN_CONFIRMATION) {
					if (Yum::module()->recoveryFromWeb) {
						if (isset($_POST['YumUserChangePassword'])) {
							$passwordform->attributes = $_POST['YumUserChangePassword'];
							if ($passwordform->validate()) {
								$user->password = YumUser::encrypt($passwordform->password);
								$user->save();
								$username = (Yum::module()->loginType & UserModule::LOGIN_BY_EMAIL) ? $email : $user->username;
								$identity = new YumUserIdentity($username, $passwordform->password);

								// handle the login task
								$this->doLogin($username, $passwordform->password);
							}
							else
							{
								$errors = $passwordform->getErrors();
								Yii::app()->user->setFlash('error', Yum::t($errors['password'][0]));
							}
						}
						// Renders the change password form
						$this->renderPasswordForm(array(
									'title' => Yum::t("Password recovery"),
									'content' => Yum::t("Your request succeeded. Please enter below your new password:"),
									), array(
										'email' => $email,
										'key' => $key,
										)
								);
						return;
					}
					$password = YumUserChangePassword::createRandomPassword();
					$user->password = YumUser::encrypt($password);
					$user->save();

					$mail = array(
							'from' => Yii::app()->params['adminEmail'],
							'to' => $user->profile->email,
							'subject' => Yum::t('Password recovery'),
							'body' => sprintf('You have requested to reset your Password. Your new password, is %s', $password),
							);
					$sent = YumMailer::send($mail);

					Yii::app()->user->setFlash('loginMessage', Yum::t('Instructions have been sent to you. Please check your email.'));
				}

				if ($user->activationKey == $_GET['key']) {
					if (isset($_POST['YumUserChangePassword'])) {
						$passwordform->attributes = $_POST['YumUserChangePassword'];
						if ($passwordform->validate()) {

							$user->password = YumUser::encrypt($passwordform->password);
							$user->activationKey = YumUser::encrypt(microtime() . $passwordform->password);
							$user->save();
							Yii::app()->user->setFlash('loginMessage', Yum::t("Your new password has been saved."));
							$this->redirect(Yum::module()->loginUrl);
						}
					}
					if ($registrationType == YumRegistration::REG_NO_PASSWORD || $registrationType == YumRegistration::REG_NO_PASSWORD_ADMIN_CONFIRMATION) {
						$this->redirect(array('/user/user/login'));
					} else {
						$this->render(Yum::module()->recoveryChangePasswordView, array('form' => $passwordform));
					}
				} else {
					Yii::app()->user->setFlash('recoveryMessage', Yum::t("Incorrect recovery link."));
					$this->redirect($this->createAbsoluteUrl('registration/recovery'));
				}
			} else {
				if (isset($_POST['YumUserRecoveryForm'])) {
					$form->attributes = $_POST['YumUserRecoveryForm'];

					if ($form->validate()) {
						$user = YumUser::model()->findbyPk($form->user_id);

						$activation_url = $this->createAbsoluteUrl('registration/recovery', array(
									'key' => $user->activationKey,
									'email' => $user->profile->email));
						if (Yum::module()->enableLogging == true)
							YumActivityController::logActivity($user, 'recovery');

						Yum::setFlash('Instructions have been sent to you. Please check your email.');

						$content = YumTextSettings::model()->find('language = :lang', array('lang' => Yii::app()->language));
						$sent = null;

						if (is_object($content)) {
							$mail = array(
									'from' => Yii::app()->params['adminEmail'],
									'to' => $user->profile->email,
									'subject' => $content->subject_email_registration,
									'body' => strtr($content->text_email_recovery, array('{activation_url}' => $activation_url)),
									);
							$sent = YumMailer::send($mail);
						} else {
							throw new CException(Yum::t('The messages for your application language are not defined.'));
						}
					}
				}
				$this->render(Yum::module()->recoveryView, array('form' => $form));
			}
		}

		/**
		 * doLogin does the login task, whether is autologin or simply show the
		 * login page. This is an attemp to DRY YumRegistrationController.
		 * @param string $username The user's username
		 * @param string $password The user's password
		 * @return None. It just redirects to the proper page.
		 */
		public function doLogin($username=null, $password=null) {
			if (Yum::module()->loginType & UserModule::LOGIN_BY_EMAIL && strpos($username, '@')) {
				$username = YumProfile::model()->findByAttributes(array('email' => $username))->user->username;
			}

			if (Yum::module()->autoLogin) {
				$identity = new YumUserIdentity($username, $password);
				$identity->authenticate();
				if ($identity->errorCode == UserIdentity::ERROR_NONE) {
					$duration = 3600 * 24 * 30; // 30 days
					Yii::app()->user->login($identity, $duration);
					$this->redirect(Yum::module()->returnUrl);
				}
			} else {
				Yii::app()->user->setFlash('success', Yum::t("Please log in into the application."));
				$this->redirect(Yum::module()->loginUrl);
			}
		}

		/**
		 * renderPasswordForm shows the password change form. This is an attemp to DRY
		 * YumRegistrationController.
		 * @param array $vars Array that contains the 'title' and 'contents'
		 * key=>value parameters.
		 */
		public function renderPasswordForm($vars=array(), $params=array()) {
			if ($vars['title'] == '' || $vars['title'] == null)
				$vars['title'] == Yum::t('Message');
			if ($vars['content'] == null)
				$vars['content'] == '';

			$partial = array();
			if (Yum::module()->activationPasswordSet || $this->action - id == 'activation')
				$params = array_merge(array('form' => new YumUserChangePassword), $params);
			$partial = array(
					array('view' => Yum::module()->passwordFormView, 'params' => $params));
			$this->render(Yum::module()->messageView, array(
						'title' => $vars['title'],
						'content' => $vars['content'],
						'partial' => $partial,
						)
					);
		}

	}
