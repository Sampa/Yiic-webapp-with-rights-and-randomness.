<?php

class YumRegistrationController extends YumController
{
	private $_model;
	public $defaultAction = 'registration';

	public function accessRules()
	{
		return array(
				array('allow',
					'actions'=>array('index', 'registration', 'recovery', 'activation'),
					'users'=>array('*'),
					),
				array('allow',
					'actions'=>array('captcha'),
					'users'=>array('*'),
					'expression'=>'Yii::app()->controller->module->enableCaptcha',
					),
				array('deny',  // deny all other users
						'users'=>array('*'),
						),
				);
	}

	public function actions()
	{
		return Yii::app()->controller->module->enableCaptcha 
			? array(
					'captcha'=>array(
						'class'=>'CCaptchaAction',
						'backColor'=>0xFFFFFF,
						),
					)
			: array();
	}

	/* 
		 Registration of an new User in the system.
	 */
	public function actionRegistration() {
		$registrationType = Yii::app()->getModule('user')->registrationType;

		if($registrationType == YumRegistration::REG_DISABLED)
			$this->redirect(Yii::app()->user->returnUrl);

		$form = new YumRegistrationForm;
		$profile = new YumProfile();

		if(isset($_POST['YumRegistrationForm'])) {
			$form->attributes = $_POST['YumRegistrationForm'];
			$form->email = $_POST['YumProfile']['email'];

			if(isset($_POST['YumProfile'])) {
				$profile->attributes = $_POST['YumProfile'];
				$profile->validate();
			}

			if($form->validate() && !$profile->hasErrors()) {
				$user = new YumUser();

				if ($user->register($form->username, $form->password, $form->email)) {
					if(isset($_POST['YumProfile'])) {
						$profile->attributes = $_POST['YumProfile'];
						$profile->user_id = $user->id;
						$profile->save();
						$user->email = $profile->attributes['email'];
					}

					if($registrationType == YumRegistration::REG_EMAIL_CONFIRMATION || 
							$registrationType == YumRegistration::REG_EMAIL_AND_ADMIN_CONFIRMATION) {
						$this->sendRegistrationEmail($user);
						Yii::app()->user->setFlash('registration',
								Yum::t("Thank you for your registration. Please check your email."));
						$this->refresh();
					} else if($registrationType == YumRegistration::REG_SIMPLE) {
						Yii::app()->user->setFlash('registration',
								Yum::t("Your account has been activated. Thank you for your registration."));
						$this->refresh();
					} 
				} else {
					Yii::app()->user->setFlash('registration',
							Yum::t("Your registration didn't work. Please contact our System Administrator."));
					$this->refresh();
				}
			}
		}

		$this->render('/user/registration', array(
					'form' => $form,
					'profile' => $profile
					)
				);
	}

	// Send the Email to the given user object. $user->email needs to be set.
	public function sendRegistrationEmail($user) {
		if(!isset($user->profile[0]->email))
		{
			throw new CException(Yii::t('UserModule.user', 'Email is not set when trying to send Registration Email'));	
		}

		$headers = "From: " . Yii::app()->params['adminEmail']."\r\nReply-To: ".Yii::app()->params['adminEmail'];
		$activation_url = 'http://' .
			$_SERVER['HTTP_HOST'] .
			$this->createUrl('user/activation',array(
						'activationKey' => $user->activationKey, 'email' => $user->profile[0]->email)
					);

		$content = YumTextSettings::model()->find('language = :lang', array(
					'lang' => Yii::app()->language));

		$msgheader = $content->header_email_registration;
		$msgbody = strtr($content->text_email_registration, array('{activation_url}' => $activation_url));

		mail($user->profile[0]->email, $msgheader, $msgbody, $headers);

		return true;
	}

	/**
	 * Activation of an user account
	 */
	public function actionActivation ()
	{
		if(YumUser::activate($_GET['email'], $_GET['activationKey'])) {
			$this->render('message', array(
						'title'=>Yum::t("User activation"),
						'content'=>Yum::t("Your account has been activated.")));
		} else {
			$this->render('message',array(
						'title'=>Yum::t("User activation"),
						'content'=>Yum::t("Incorrect activation URL")));
		}
	}

	/**
	 * Password recovery routine. The User will be sent an email with an
	 * activation link. If clicked, he will be prompted to enter his new 
	 * password.
	 */
	public function actionRecovery () {
		$form = new YumUserRecoveryForm;

		if (isset($_GET['email']) && isset($_GET['activationKey'])) {
			$passwordform = new YumUserChangePassword;
			$user = YumProfile::model()->findByAttributes(array('email'=>$email))->user;
			if($user->activationKey == $_GET['activationKey']) {
				if(isset($_POST['YumUserChangePassword'])) {
					$passwordform->attributes = $_POST['YumUserChangePassword'];
					if($passwordform->validate()) {
						$user->password = YumUser::encrypt($form2->password);
						$user->activationKey = YumUser::encrypt(microtime().$passwordform->password);
						$user->save();

						Yii::app()->user->setFlash('loginMessage',
								Yii::t("user", "Your new password has been saved."));
						$this->redirect(Yii::app()->controller->module->loginUrl);
					}
				}
				$this->render('changepassword',array('form'=>$passwordform));
			} else {
				Yii::app()->user->setFlash('recoveryMessage',
						Yii::t("user", "Incorrect recovery link."));
				$this->redirect('http://' . $_SERVER['HTTP_HOST'] . $this->createUrl('user/recovery'));
			}
		} else {
			if(isset($_POST['YumUserRecoveryForm'])) {
				$form->attributes=$_POST['YumUserRecoveryForm'];

				if($form->validate()) {
					$user = YumUser::model()->findbyPk($form->user_id);
					$headers = sprintf('From: %s\r\nReply-To: %s',
							Yii::app()->params['adminEmail'],
							Yii::app()->params['adminEmail']);

					$activation_url = sprintf('http://%s%s',
							$_SERVER['HTTP_HOST'],
							$this->createUrl('user/recovery',array(
									'activationKey' => $user->activationKey,
									'email' => $user->email)));

					mail($user->profile[0]->email,
							Yum::t('Password recovery'), 
							sprintf('You have requested to reset your Password. To receive a new password, go to %s',
								$activation_url),$headers);

					Yii::app()->user->setFlash('loginMessage',
							Yii::t('UserModule.user',
								'Instructions have been sent to you. Please check your email.'));

					$this->redirect(array('/user/user/login'));
				}
			}
			$this->render('recovery',array('form'=>$form));
		}
	}

}
