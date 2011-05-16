<?php

/* This file handles a example registration process logic and some of the
 * most used functions for Registration and Activation. It is recommended to
 * extend from this class and implement your own, project-specific 
 * Registration process. If this example does exactly what you want in your
 * Project, then you can feel lucky already! */

Yii::import('application.modules.user.controllers.YumController');
Yii::import('application.modules.user.models.*');
Yii::import('application.modules.profile.models.*');
Yii::import('application.modules.registration.models.*');

class YumRegistrationController extends YumController {
	public $defaultAction = 'registration';

	// Only allow the registration if the user is not already logged in and
	// the function is activated in the Module Configuration
	public function beforeAction($action) {
		if (!Yii::app()->user->isGuest) 
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
	 * please see the documentation of yii-user-management for examples on how to
	 * extend from this class and implement your own registration logic 
	 */
	public function actionRegistration() {
		Yii::import('application.modules.profile.models.*');
		$form = new YumRegistrationForm;
		$profile = new YumProfile;

		$this->performAjaxValidation('YumRegistrationForm', $form);

		if (isset($_POST['YumRegistrationForm'])) { 
			$form->attributes = $_POST['YumRegistrationForm'];
			$profile->attributes = $_POST['YumProfile'];

			$form->validate();
			$profile->validate();

			if(!$form->hasErrors() && !$profile->hasErrors()) {
				$user = new YumUser;
				$user->register($form->username, $form->password, $profile->email);
				$profile->user_id = $user->id;
				$profile->save();

				$this->sendRegistrationEmail($user);
				Yum::setFlash('Thank you for your registration. Please check your email.');
				$this->redirect(Yum::module()->loginUrl);
			}
		} 

		$this->render(Yum::module()->registrationView, array(
					'form' => $form,
					'profile' => $profile,
					)
				);  
	}

		// Send the Email to the given user object. $user->email needs to be set.
		public function sendRegistrationEmail($user) {
			if (!isset($user->profile->email)) {
				throw new CException(Yum::t('Email is not set when trying to send Registration Email'));
			}
			$activation_url = $user->getActivationUrl();

			// get the text to sent from the yumtextsettings table
			$content = YumTextSettings::model()->find('language = :lang', array(
						'lang' => Yii::app()->language));
			$sent = null;

			if (is_object($content)) {
					$body = strtr($content->text_email_registration, array(
								'{username}' => $user->username,
								'{activation_url}' => $activation_url));

				$mail = array(
						'from' => Yum::module('registration')->registrationEmail,
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
		public function actionActivation($email, $key) {
			// If already logged in, we dont activate anymore
			if (!Yii::app()->user->isGuest)
				$this->redirect(Yii::app()->user->returnUrl);

			// If everything is set properly, let the model handle the Validation
			// and do the Activation
				$status = YumUser::activate($email, $key);

				if($status instanceof YumUser)
					$this->render(Yum::module('registration')->activationSuccessView);
				else
					$this->render(Yum::module('registration')->activationFailureView, array(
								'error' => $status));
		}

		/**
		 * Password recovery routine. The User will receive an email with an
		 * activation link. If clicked, he will be prompted to enter his new
		 * password.
		 */
		public function actionRecovery($email = null, $key = null) {
			$form = new YumUserRecoveryForm;

			if ($email != null && $key != null) {
				if($profile = YumProfile::model()->find("email = '{$email}'")) {
						$user = $profile->user;
						if($user->activationKey == $key) {
							$passwordform = new YumUserChangePassword;
							if (isset($_POST['YumUserChangePassword'])) {
								$passwordform->attributes = $_POST['YumUserChangePassword'];
									if ($passwordform->validate()) {
										$user->password = YumUser::encrypt($passwordform->password);
										$user->activationKey = YumUser::encrypt(microtime() . $passwordform->password);
										$user->save();
										Yum::setFlash('Your new password has been saved.');
										$this->redirect(Yum::module()->loginUrl);
									}
							}
							$this->render(Yum::module()->recoveryChangePasswordView, array('form' => $passwordform));
						}
				}
			} else {
				if (isset($_POST['YumUserRecoveryForm'])) {
						$form->attributes = $_POST['YumUserRecoveryForm'];

						if ($form->validate()) {
							$user = YumUser::model()->findbyPk($form->user_id);

							$activation_url = $this->createAbsoluteUrl('registration/recovery', array(
										'key' => $user->activationKey,
										'email' => $user->profile->email));
							if (Yum::module()->enableLogging)
								Yum::log(Yum::t(
											'{username} requested a new password in the password recovery form', array(
												'{username}' => $user->username)));


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

							Yum::setFlash('Instructions have been sent to you. Please check your email.');
							$this->redirect(Yum::module()->loginUrl);
						} else {
							throw new CException(Yum::t('The messages for your application language are not defined.'));
						}
					}
				}
				$this->render(Yum::module()->recoveryView, array('form' => $form));
			}
		}
	}
