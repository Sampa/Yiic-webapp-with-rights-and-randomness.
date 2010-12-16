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
			if(Yum::module()->loginType & UserModule::LOGIN_BY_EMAIL)
			{
				$form->email = $form->username;
				$form->username = YumRegistrationForm::genRandomString($usernameRequirements['maxLen']);
			}

			if(isset($_POST['YumProfile'])) {
				$profile->attributes = $_POST['YumProfile'];
				if(Yum::module()->loginType & UserModule::LOGIN_BY_EMAIL)
					$profile->email = $form->email;
				$profile->validate();
			}

			if($form->email != '' && YumProfile::model()->find('email = "'.$form->email .'"')) 
			$profile->addError('email', Yum::t('E-Mail already in use. If you have not registered before, please contact our System administrator.'));
			
			
			if($registrationType == YumRegistration::REG_NO_PASSWORD  
					|| $registrationType == YumRegistration::REG_NO_PASSWORD_ADMIN_CONFIRMATION) {
				$form->password=YumUserChangePassword::createRandomPassword(
						Yum::module()->passwordRequirements['minLowerCase'],
						Yum::module()->passwordRequirements['minUpperCase'],
						Yum::module()->passwordRequirements['minDigits'],
						Yum::module()->passwordRequirements['minLen']); 
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
					if($registrationType == YumRegistration::REG_EMAIL_CONFIRMATION 
							|| $registrationType == YumRegistration::REG_EMAIL_AND_ADMIN_CONFIRMATION 
							|| $registrationType == YumRegistration::REG_NO_PASSWORD 
							|| $registrationType == YumRegistration::REG_NO_PASSWORD_ADMIN_CONFIRMATION ) 
					{
						$success=$this->sendRegistrationEmail($user,$form->password);

						Yii::app()->user->setFlash('registration',
								Yum::t("Thank you for your registration. Please check your email."));
						$this->actionActivate($user,$form);
						Yii::app()->end();
					}
					else if($registrationType == YumRegistration::REG_SIMPLE) {
						Yii::app()->user->setFlash('registration',
								Yum::t("Your account has been activated. Thank you for your registration."));
						$this->refresh();
					} 
				}
				else
				{
					Yii::app()->user->setFlash('registration',
							Yum::t("Your registration didn't work. Please try another E-Mail address. If this problem persists, please contact our System Administrator. "));
					$this->refresh();
				}
			}
			else
			{
				$form->addErrors($profile->getErrors());
			}
		}

		if(Yum::module()->loginType & UserModule::LOGIN_BY_EMAIL)
			$form->username = $form->email;

		$this->render('/user/registration', array(
					'form' => $form,
					'profile' => $profile,
					'registrationtype'=>$registrationType,
					)
				);
	}

	public function actionActivate($user=null,$form=null)
	{
		if (!isset($user) && isset($_POST['YumProfile']['email']))
		{
			$email=$_POST['YumProfile']['email'];
			$profile=YumProfile::model()->findAll($condition='email = :email',array(':email'=>$email));
			$user=$profile[0]->user;
		}
		else
		{
			$user=new YumUser;
		}
		if(!isset($form))
		{
			$form = new YumRegistrationForm;
		}

		$this->render('/user/resend_activation', array(
					'form' => $form,
					'user'=>$user,
					'activateFromWeb'=>Yum::module()->activateFromWeb,
					)
				);
	}
	
	public function actionResendActivation()
	{
		
		if(isset($_POST['email']))
		{
			$email=$_POST['email'];
			$registrationType = Yum::module()->registrationType;
			$password=null;
			$profile=YumProfile::model()->findAll($condition='email = :email',array(':email'=>$email));
			$user=$profile[0]->user;
			$user->activationKey=$user->generateActivationKey();
			if($registrationType == YumRegistration::REG_NO_PASSWORD  || $registrationType == YumRegistration::REG_NO_PASSWORD_ADMIN_CONFIRMATION)
			{
				$password=YumUserChangePassword::createRandomPassword(Yum::module()->passwordRequirements['minLowerCase'],Yum::module()->passwordRequirements['minUpperCase'],Yum::module()->passwordRequirements['minDigits'],Yum::module()->passwordRequirements['minLen']); 
				$user->password=YumUser::model()->encrypt($password);
			}
			$user->save();
			
		}
		else
		{
			if(!isset($user) && !isset($_POST['email']))
				$user= new YumUser;
		}
		$form = new YumRegistrationForm;
		$this->render('/user/resend_activation', array(
					'form' => $form,
					'user'=>$user,
					)
				);	
		return $success;
	}
	
	// Send the Email to the given user object. $user->email needs to be set.
	public function sendRegistrationEmail($user,$password=null)
	{
		if(!isset($user->profile[0]->email))
		{
			throw new CException(Yum::t('Email is not set when trying to send Registration Email'));	
		}
			$registrationType = Yum::module()->registrationType;
			$headers = "From: " . Yum::module()->registrationEmail ."\r\nReply-To: ".Yii::app()->params['adminEmail'];

			$activation_url = 'http://' .  $_SERVER['HTTP_HOST'] .  $this->createUrl('registration/activation',array(
				'activationKey' => $user->activationKey,
				'email' => $user->profile[0]->email)
					);

			//Send mail, Make it DRY!!!
			$content = YumTextSettings::model()->find('language = :lang', array(
						'lang' => Yii::app()->language));
			$sent=null;

			if(is_object($content)) {
				$msgheader = $content->subject_email_registration;
				if($registrationType == YumRegistration::REG_NO_PASSWORD  || $registrationType == YumRegistration::REG_NO_PASSWORD_ADMIN_CONFIRMATION)
				{
					$msgbody = strtr($content->text_email_registration . "\n\nYour Activation Key is $user->activationKey ,\n\n Your temporary password is $password,", array('{activation_url}' => $activation_url));
				}
				else
				{
					$msgbody = strtr($content->text_email_registration, array('{activation_url}' => $activation_url));
				}
				if(Yum::module()->mailer == 'swift')
				{
					$sm = Yii::app()->swiftMailer;
					$mailer = $sm->mailer($sm->mailTransport());
					$message = $sm->newMessage($msgheader)   
						->setFrom(Yum::module()->registrationEmail)
						->setTo($user->profile[0]->email)
						->setBody($msgbody);                                                    
					$sent=$mailer->send($message);
				}
				elseif(Yum::module()->mailer == 'PHPMailer')
				{
					Yii::import('application.extensions.phpmailer.JPhpMailer');
					$mail = new JPhpMailer(true);
					if (Yum::module()->phpmailer['transport'])
						switch (Yum::module()->phpmailer['transport'])
						{
							case 'smtp':
								$mail->IsSMTP();
								break;
							case 'sendmail':
								$mail->IsSendmail();
								break;
							case 'qmail':
								$mail->IsQmail();
								break;
							case 'mail':
							default:
								$mail->IsMail();
						}
					else
						$mail->IsMail();

					if (Yum::module()->phpmailer['html'])
						$mail->IsHTML(Yum::module()->phpmailer['html']);
					else
						$mail->IsHTML(false);

					try
					{
						$mailconf=Yum::module()->phpmailer['properties'];
						if(is_array($mailconf))
						{
							foreach($mailconf as $key=>$value)
							{
								if(isset(JPhpMailer::${$key}))
								{
									JPhpMailer::${$key} = $value;
								}
								else
								{
									$mail->$key=$value;
								}
							}
						}
						$mail->AddAddress($user->profile[0]->email, Yum::module()->phpmailer['msgOptions']['toName']); //FIXME
						$mail->SetFrom(Yii::app()->params['adminEmail'], Yum::module()->phpmailer['msgOptions']['fromName']); //FIXME
						$mail->Subject = $content->subject_email_registration;
						$mail->Body = $msgbody;
						$mail->Send();
					}
					catch (phpmailerException $e)
					{
						throw new CException($e->errorMessage());
					}
					catch (Exception $e)
					{
						throw new CException($e->getMessage());
					}
				}
				else
				{
					$sent=mail($user->profile[0]->email, $msgheader, $msgbody, $headers);
				}
			}
			else
			{
				throw new CException(Yum::t('no object'));
			}

			return $sent;
	}

	/**
	 * Activation of an user account
	 */
	public function actionActivation ($email=null,$key=null)
	{
		if(isset($_POST['YumUserChangePassword']))
		{
				//FIX ME: Ugly hack to pass email and key on $_POST
				$email=$_POST['email'];
				$key=$_POST['activationKey'];

				if(YumUser::activate($email,$key))
				{
					$form = new YumUserChangePassword;
					$form->attributes = $_POST['YumUserChangePassword'];

					if($form->validate())
					{
						$profile=YumProfile::model()->find('email=\''.$email.'\'');
						$user=$profile->user;
						$user->password=YumUser::encrypt($form->password);
						$user->save();

						//do autoLogin
						if (Yum::module()->autoLogin)
						{
							$username = (Yum::module()->loginType & UserModule::LOGIN_BY_EMAIL) ? $email : $user->username;
							$identity=new YumUserIdentity($username, $form->password);
							$identity->authenticate();
							if ($identity->errorCode == UserIdentity::ERROR_NONE)
							{
								$duration= 3600*24*30; // 30 days
					    		Yii::app()->user->login($identity,$duration);
								$this->redirect($this->createUrl(Yum::module()->profileView));
							}
						}
						else
						{
							Yii::app()->user->setFlash('loginMessage',
								Yum::t("Please log in into the application."));
							$this->redirect(Yum::module()->loginUrl);
						}
					}
					else
					{
						Yii::app()->user->setFlash('loginMessage', Yum::t("Cannot set password. Try again."));
						$partial = array();
						if(Yum::module()->activationPasswordSet)
							$partial = array(
								//array('view'=>'/user/passwordfields', 'params'=> array()),
								array(  'view' => '/user/_activation_passwordform',
									'params' => array(
									'form' => $form,
									'email' => $email,
									'key' => $key,
									),
								),
							);
						$this->render('/user/message', array(
							'title'=>Yum::t("User activation"),
							'content'=>Yum::t("Cannot set password. Try again."),
							'partial'=>$partial,
							)
						);
					}
			}
		}

		if(isset($_GET['email']) && isset($_GET['activationKey']))
		{
			$email=$_GET['email'];
			$key=$_GET['activationKey'];
		}
		
		if(YumUser::activate($email, $key))
		{
			$partial = array();
			if(Yum::module()->activationPasswordSet)
					$partial = array(
						//array('view'=>'/user/passwordfields', 'params'=> array()),
							array(  'view' => '/user/_activation_passwordform',
									'params' => array(
										'form' => new YumUserChangePassword,
										'email' => $email,
										'key' => $key,
									),
							),
						);
			$this->render('/user/message', array(
						'title'=>Yum::t("User activation"),
						'content'=>Yum::t("Your account has been activated."),
						'partial'=>$partial,
				)
			);
		}
		else
		{
			$this->actionActivate();
/*
			$this->render('/user/message',array(
						'title'=>Yum::t("User activation"),
						'content'=>Yum::t("Incorrect activation URL")));
*/
		}
	}

	/**
	 * Password recovery routine. The User will be sent an email with an
	 * activation link. If clicked, he will be prompted to enter his new 
	 * password.
	 */
	public function actionRecovery () {
		$form = new YumUserRecoveryForm;
		
		$headers = sprintf("From: %s\r\nReply-To: %s",
							Yum::module()->recoveryEmail,
							Yum::module()->recoveryEmail);
							
		if (isset($_GET['email']) && isset($_GET['activationKey']))
		{
			$email=$_GET['email'];
			$key=$_GET['activationKey'];
			$registrationType = Yum::module()->registrationType;
			$passwordform = new YumUserChangePassword;
			$user = YumProfile::model()->findByAttributes(
					array('email'=>$_GET['email']))->user;
			
			if($registrationType == YumRegistration::REG_NO_PASSWORD  || $registrationType == YumRegistration::REG_NO_PASSWORD_ADMIN_CONFIRMATION)
			{
				if(Yum::module()->recoveryFromWeb)
				{
					if(isset($_POST['YumUserChangePassword']))
					{
						$passwordform->attributes = $_POST['YumUserChangePassword'];
						if ($passwordform->validate())
						{
							$user->password = YumUser::encrypt($passwordform->password);
							$user->save();
							if (Yum::module()->autoLogin)
							{
								$username = (Yum::module()->loginType & UserModule::LOGIN_BY_EMAIL) ? $email : $user->username;
								$identity=new YumUserIdentity($username, $passwordform->password);
								$identity->authenticate();
								if ($identity->errorCode == UserIdentity::ERROR_NONE)
								{
									$duration= 3600*24*30; // 30 days
						    		Yii::app()->user->login($identity,$duration);
									$this->redirect($this->createUrl(Yum::module()->profileView));
								}
							}
							else
							{
								Yii::app()->user->setFlash('loginMessage', Yum::t("Please log in into the application."));
								$this->redirect(Yum::module()->loginUrl);
							}
						}
					}
					// Renders the change password form
					$partial = array(
						array(  'view' => '/user/_activation_passwordform',
								'params' => array(
									'form' => $passwordform,
									'email' => $email,
									'key' => $key,
								),
						),
					);
					$this->render('/user/message', array(
								'title'=>Yum::t("Password recovery"),
								'content'=>Yum::t("Your request succeeded. Please enter below your new password:"),
								'partial'=>$partial,
						)
					);
					return;
				}
				$password=YumUserChangePassword::createRandomPassword(Yum::module()->passwordRequirements['minLowerCase'],Yum::module()->passwordRequirements['minUpperCase'],Yum::module()->passwordRequirements['minDigits'],Yum::module()->passwordRequirements['minLen']);
			    $user->password = YumUser::encrypt($password);
			    $user->save();
				
				mail($user->profile[0]->email,
				Yum::t('Password recovery'), 
				sprintf('You have requested to reset your Password. Your new password, is %s',
				$password),$headers);
								
				Yii::app()->user->setFlash('loginMessage',
				Yum::t('Instructions have been sent to you. Please check your email.'));
			}

			if($user->activationKey == $_GET['activationKey']) {
				if(isset($_POST['YumUserChangePassword'])) {
					$passwordform->attributes = $_POST['YumUserChangePassword'];
					if($passwordform->validate()) {
						
				$user->password = YumUser::encrypt($passwordform->password);
				$user->activationKey = YumUser::encrypt(microtime().$passwordform->password);
				$user->save();
				Yii::app()->user->setFlash('loginMessage',
								Yum::t("Your new password has been saved."));
						$this->redirect(Yum::module()->loginUrl);
					}
				}
				if($registrationType == YumRegistration::REG_NO_PASSWORD  || $registrationType == YumRegistration::REG_NO_PASSWORD_ADMIN_CONFIRMATION){
					$this->redirect(array('/user/user/login'));
				}else{
				$this->render('/user/changepassword',array('form'=>$passwordform));
			}
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

			

					$activation_url = sprintf('http://%s%s',
							$_SERVER['HTTP_HOST'],
							$this->createUrl('registration/recovery',array(
									'activationKey' => $user->activationKey,
									'email' => $user->profile[0]->email)));
					if(Yum::module()->enableLogging == true)
					YumActivityController::logActivity($user, 'recovery');
					Yii::app()->user->setFlash('loginMessage',
							Yum::t('Instructions have been sent to you. Please check your email.'));

					//Send mail, Make it DRY!!!
					$content = YumTextSettings::model()->find('language = :lang', array('lang' => Yii::app()->language));
					$sent=null;

					if(is_object($content))
					{
						$msgheader = $content->subject_email_registration;
						$msgbody = strtr($content->text_email_recovery, array('{activation_url}' => $activation_url));

						if(Yum::module()->mailer == 'swift')
						{
							$sm = Yii::app()->swiftMailer;
							$mailer = $sm->mailer($sm->mailTransport());
							$message = $sm->newMessage($msgheader)
								->setFrom(Yii::app()->params['adminEmail'])
								->setTo($user->profile[0]->email)
								->setBody($msgbody);
							$sent=$mailer->send($message);
						}
						elseif(Yum::module()->mailer == 'PHPMailer')
						{
							Yii::import('application.extensions.phpmailer.JPhpMailer');
							$mail = new JPhpMailer(true);
							if (Yum::module()->phpmailer['transport'])
								switch (Yum::module()->phpmailer['transport'])
								{
									case 'smtp':
										$mail->IsSMTP();
										break;
									case 'sendmail':
										$mail->IsSendmail();
										break;
									case 'qmail':
										$mail->IsQmail();
										break;
									case 'mail':
									default:
										$mail->IsMail();
								}
							else
								$mail->IsMail();

							if (Yum::module()->phpmailer['html'])
								$mail->IsHTML(Yum::module()->phpmailer['html']);
							else
								$mail->IsHTML(false);

							try
							{
								$mailconf=Yum::module()->phpmailer['properties'];
								if(is_array($mailconf))
								{
									foreach($mailconf as $key=>$value)
									{
										if(isset(JPhpMailer::${$key}))
										{
											JPhpMailer::${$key} = $value;
										}
										else
										{
											$mail->$key=$value;
										}
									}
								}
								$mail->AddAddress($user->profile[0]->email, Yum::module()->phpmailer['msgOptions']['toName']); //FIXME
								$mail->SetFrom(Yii::app()->params['adminEmail'], Yum::module()->phpmailer['msgOptions']['fromName']); //FIXME
								$mail->Subject = $content->subject_email_registration;
								$mail->Body = $msgbody;
								$mail->Send();
							}
							catch (phpmailerException $e)
							{
								$e_message=$e->errorMessage();
							}
							catch (Exception $e)
							{
								$e_message=$e->getMessage();
							}
						}
						else
						{
							$sent=mail($user->profile[0]->email, $msgheader, $msgbody, $headers);
						}
					}
					else
					{
						throw new CException(Yum::t('no object'));
					}

					#$this->redirect(array('/user/user/login'));
				}
			}
			$this->render('/user/recovery',array('form'=>$form));
		}
	}
}