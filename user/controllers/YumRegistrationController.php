<?php

Yii::import('application.modules.user.controllers.YumController');

class YumRegistrationController extends YumController
{
	private $_model;
	public $defaultAction = 'registration';

	public function accessRules()
	{
		return array(
				array('allow',
					'actions'=>array('index', 'registration', 'recovery', 'activation','resendactivation'),
					'users'=>array('*'),
					),
				array('allow',
					'actions'=>array('captcha'),
					'users'=>array('*'),
					'expression'=>'Yii::app()->getModule(\'user\')->enableCaptcha',
					),
				array('deny',  // deny all other users
						'users'=>array('*'),
						),
				);
	}

	public function actions()
	{
		return array(
				'captcha'=>array(
					'class'=>'CCaptchaAction',
					'backColor'=>0xFFFFFF,
					),
				);
	}

	/* 
		 Registration of an new User in the system.
	 */
	public function actionRegistration() {
		$registrationType = Yum::module()->registrationType;
		$loginType = Yum::module()->loginType;
		$usernameRequirements= Yum::module()->usernameRequirements;

		if($registrationType == YumRegistration::REG_DISABLED)
			$this->redirect(Yii::app()->user->returnUrl);

		$form = new YumRegistrationForm;
		$profile = new YumProfile();

		if(isset($_POST['YumRegistrationForm'])) {
			$form->attributes = $_POST['YumRegistrationForm'];
			$form->email = $_POST['YumProfile']['email'];
			if($loginType == 'LOGIN_BY_EMAIL' )  
			{
			$form->username = YumRegistrationForm::genRandomString($usernameRequirements['maxLen']);
			}

			if(isset($_POST['YumProfile'])) {
				$profile->attributes = $_POST['YumProfile'];
				$profile->validate();
			}

			if($form->email != '' && YumProfile::model()->find('email = "'.$form->email .'"')) 
			$profile->addError('email', Yum::t('E-Mail already in use. If you have not registered before, please contact our System administrator.'));
			
			
			if($registrationType == YumRegistration::REG_NO_PASSWORD  || $registrationType == YumRegistration::REG_NO_PASSWORD_ADMIN_CONFIRMATION)
			{
			$form->password=YumUserChangePassword::createRandomPassword(Yum::module()->passwordRequirements['minLowerCase'],Yum::module()->passwordRequirements['minUpperCase'],Yum::module()->passwordRequirements['minDigits'],Yum::module()->passwordRequirements['minLen']); 
			$form->verifyPassword=$form->password;
			}
			
			if($form->validate() && !$profile->hasErrors()) 
			{
			$user = new YumUser();
			if(isset($_POST['roles']) && is_numeric($_POST['roles']))
			$user->roles = array($_POST['roles']);
			if(isset($_POST['roles']) && is_array($_POST['roles']))
			$user->roles = $_POST['roles'];
			if ($user->register($form->username, $form->password, $form->email)) 
			{
			if(isset($_POST['YumProfile'])) 
			{
			$profile->user_id = $user->id;
			$profile->save();
			}

					//YumActivityController::log($user, 'register');
			if($registrationType == YumRegistration::REG_EMAIL_CONFIRMATION || 
			 $registrationType == YumRegistration::REG_EMAIL_AND_ADMIN_CONFIRMATION || $registrationType == YumRegistration::REG_NO_PASSWORD || $registrationType == YumRegistration::REG_NO_PASSWORD_ADMIN_CONFIRMATION ) 
			{
			$success=$this->sendRegistrationEmail($user,$form->password);
			
				Yii::app()->user->setFlash('registration',
					Yum::t("Thank you for your registration. Please check your email."));
					$this->actionActivate($user,$form);
					Yii::app()->end();
					
					} else if($registrationType == YumRegistration::REG_SIMPLE) 
					{
						Yii::app()->user->setFlash('registration',
								Yum::t("Your account has been activated. Thank you for your registration."));
						$this->refresh();
					} 
				} else {
					Yii::app()->user->setFlash('registration',
							Yum::t("Your registration didn't work. Please try another E-Mail address. If this problem persists, please contact our System Administrator. "));
					$this->refresh();
				}
			}else{
				$form->addErrors($profile->getErrors());
			}
		}

		$this->render('/user/registration', array(
					'form' => $form,
					'profile' => $profile,
					'registrationtype'=>$registrationType,
					)
				);
	}

	public function actionActivate($user=null,$form=null)
	{
		
		$this->render('/user/resend_activation', array(
					'form' => $form,
					'user'=>$user,
					)
					);	
	}
	
	public function actionResendActivation()
	{
		$registrationType = Yum::module()->registrationType;
		$email=$_POST['YumRegistrationForm']['email'];
		//$email='mithereal@gmail.com';
		$profile=YumProfile::model()->findAll($condition='email = :email',array(':email'=>$email));
		$user=$profile[0]->user;
		if($registrationType == YumRegistration::REG_NO_PASSWORD  || $registrationType == YumRegistration::REG_NO_PASSWORD_ADMIN_CONFIRMATION)
			{
			$password=YumUserChangePassword::createRandomPassword(Yum::module()->passwordRequirements['minLowerCase'],Yum::module()->passwordRequirements['minUpperCase'],Yum::module()->passwordRequirements['minDigits'],Yum::module()->passwordRequirements['minLen']); 
			$user->password=YumUser::model()->encrypt($password);
			$user->save();
			}
		$this->sendRegistrationEmail($user,$password);
		$this->redirect(Yii::app()->controller->module->loginUrl);
	}
	
	// Send the Email to the given user object. $user->email needs to be set.
	public function sendRegistrationEmail($user,$password=null) {
		if(!isset($user->profile[0]->email))
		{
			throw new CException(Yum::t('Email is not set when trying to send Registration Email'));	
		}

			$headers = "From: " . Yii::app()->params['adminEmail']."\r\nReply-To: ".Yii::app()->params['adminEmail'];

			$activation_url = 'http://' .  $_SERVER['HTTP_HOST'] .  $this->createUrl('registration/activation',array(
				'activationKey' => $user->activationKey,
				'email' => $user->profile[0]->email)
					);

			$content = YumTextSettings::model()->find('language = :lang', array(
						'lang' => Yii::app()->language));
			$sent=null;

			if(is_object($content)) {
				$msgheader = $content->subject_email_registration;
				if(YumRegistration::REG_NO_PASSWORD  || YumRegistration::REG_NO_PASSWORD_ADMIN_CONFIRMATION)
				{
				$msgbody = strtr($content->text_email_registration . "\n\nYour Activation Key is $user->activationKey ,\n\n Your temporary password is $password,", array('{activation_url}' => $activation_url));
				}else{
				$msgbody = strtr($content->text_email_registration, array('{activation_url}' => $activation_url));
			}
				if(Yum::module()->mailer == 'swift') {
					$sm = Yii::app()->swiftMailer;
					$mailer = $sm->mailer($sm->mailTransport());
					$message = $sm->newMessage($msgheader)   
						->setFrom(Yii::app()->params['adminEmail'])
						->setTo($user->profile[0]->email)
						->setBody($msgbody);                                                    
					$sent=$mailer->send($message);
				} else {
					$sent=mail($user->profile[0]->email, $msgheader, $msgbody, $headers);
				}
			}else{
			throw new CException(Yum::t('no object'));	
		}

			return $sent;
	}

	/**
	 * Activation of an user account
	 */
	public function actionActivation ($email=null,$key=null)
	{
		if(isset($_GET['email']) && isset($_GET['activationKey']))
		{
		$email=$_GET['email'];
		$key=$_GET['activationKey'];
		}
		
		if(YumUser::activate($email, $key)) {
			$this->render('/user/message', array(
						'title'=>Yum::t("User activation"),
						'content'=>Yum::t("Your account has been activated.")));
		} else {
			$this->render('/user/message',array(
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
			$user = YumProfile::model()->findByAttributes(
					array('email'=>$_GET['email']))->user;

			if($user->activationKey == $_GET['activationKey']) {
				if(isset($_POST['YumUserChangePassword'])) {
					$passwordform->attributes = $_POST['YumUserChangePassword'];
					if($passwordform->validate()) {
						$user->password = YumUser::encrypt($passwordform->password);
						$user->activationKey = YumUser::encrypt(microtime().$passwordform->password);
						$user->save();

						Yii::app()->user->setFlash('loginMessage',
								Yum::t("Your new password has been saved."));
						$this->redirect(Yii::app()->controller->module->loginUrl);
					}
				}
				$this->render('/user/changepassword',array('form'=>$passwordform));
			} else {
				Yii::app()->user->setFlash('recoveryMessage',
						Yum::t("Incorrect recovery link."));
				$this->redirect('http://' . $_SERVER['HTTP_HOST'] . $this->createUrl('registration/recovery'));
			}
		} else {
			if(isset($_POST['YumUserRecoveryForm'])) {
				$form->attributes = $_POST['YumUserRecoveryForm'];

				if($form->validate()) {
					$user = YumUser::model()->findbyPk($form->user_id);

					$headers = sprintf("From: %s\r\nReply-To: %s",
							Yii::app()->params['adminEmail'],
							Yii::app()->params['adminEmail']);


					$activation_url = sprintf('http://%s%s',
							$_SERVER['HTTP_HOST'],
							$this->createUrl('registration/recovery',array(
									'activationKey' => $user->activationKey,
									'email' => $user->profile[0]->email)));

					YumActivityController::logActivity($user, 'recovery');
					mail($user->profile[0]->email,
							Yum::t('Password recovery'), 
							sprintf('You have requested to reset your Password. To receive a new password, go to %s',
								$activation_url),$headers);

					Yii::app()->user->setFlash('loginMessage',
							Yum::t('Instructions have been sent to you. Please check your email.'));

					$this->redirect(array('/user/user/login'));
				}
			}
			$this->render('/user/recovery',array('form'=>$form));
		}
	}

}
