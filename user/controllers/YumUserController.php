<?php

class YumUserController extends YumController
{
	const PAGE_SIZE=10;
	private $_model;

	public function accessRules()
	{
		return array(
				array('allow',
					'actions'=>array('index','view','registration','login', 'recovery', 'activation'),
					'users'=>array('*'),
					),
				array('allow',
					'actions'=>array('captcha'),
					'users'=>array('*'),
					'expression'=>'Yii::app()->controller->module->enableCaptcha',
					),
				array('allow',
					'actions'=>array('profile', 'edit', 'logout', 'changepassword', 'passwordexpired', 'delete'),
					'users'=>array('@'),
					),
				array('allow',
					'actions'=>array('admin','stats','delete','create','update', 'list', 'assign'),
					'users'=>array(Yii::app()->user->name ),
					'expression' => 'Yii::app()->user->isAdmin()'
					),
				array('allow',
					'actions' => array('admin'),
					'expression' => "Yii::app()->user->hasUsers()",
					),
				array('allow',
						'actions' => array('update'),
						'expression' => 'Yii::app()->user->hasUser($_GET[\'id\'])',
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

	public function actionIndex()
	{
		// If the user is not logged in, so we redirect to the actionLogin,
		// which will render the login Form

		if(Yii::app()->user->isGuest)
			$this->actionLogin();
		else if(Yii::app()->user->isAdmin())
			$this->actionStats();
		else if(isset($_GET['id']) || isset ($_GET['user_id']))
			$this->actionProfile();
		else
			$this->actionList();
	}

	public function actionStats()
	{
		$this->render('statistics', array(
					'active_users' => YumUser::model()->count('status = 1'),
					'inactive_users' => YumUser::model()->count('status = 0'),
					'admin_users' => YumUser::model()->count('superuser = 1'),
					'roles' => YumRole::model()->count(),
					'profiles' => YumProfile::model()->count(),
					'profile_fields' => YumProfileField::model()->count(),
					'profile_field_groups' => YumProfileFieldsGroup::model()->count(),
					'messages' => YumMessage::model()->count(),
					));
	}

	/* 
		 Registration of an new User in the system.
		 Depending on whether $enableEmailRegistration is set, an confirmation Email
		 will be sent to the Email address.
	 */
	public function actionRegistration()
	{
		$form = new YumRegistrationForm;
		$profile = new YumProfile();

		if(isset($_POST['YumProfile']))
			$profile->attributes = $_POST['YumProfile'];

		if(isset($_POST['YumRegistrationForm']))
		{
			$form->attributes = $_POST['YumRegistrationForm'];
			$form->email = $_POST['YumProfile']['email'];

			if(isset($_POST['YumProfile'])) {
				$profile->attributes = $_POST['YumProfile'];
				$profile->validate();
			}

			if($form->validate()) {
				$user = new YumUser();

				if ($user->register($form->username, $form->password, $form->email)) {
					if(isset($_POST['YumProfile'])) {
						$profile->attributes = $_POST['YumProfile'];
						$profile->user_id = $user->id;
						$profile->save();
						$user->email = $profile->attributes['email'];
					}

					if(Yii::app()->controller->module->enableEmailActivation) {
						$this->sendRegistrationEmail($user);
					} else {
						Yii::app()->user->setFlash('registration',
								Yii::t("UserModule.user",
									"Your account has been activated. Thank you for your registration."));
						$this->refresh();
					}

					if (UserModule::$allowInactiveAcctLogin) {
						if (Yii::app()->user->allowAutoLogin) {
							$identity = new YumUserIdentity($model->username,$sourcePassword);
							$identity->authenticate();
							Yii::app()->user->login($identity, 0);
							$this->redirect(Yii::app()->controller->module->returnUrl);
						} else {
							Yii::app()->user->setFlash('registration',
									Yii::t("UserModule.user",
										"Thank you for your registration. Please check your email or login."));
							$this->refresh();
						}
					} else {
						Yii::app()->user->setFlash('registration',
								Yii::t("UserModule.user",
									"Thank you for your registration. Please check your email."));
						$this->refresh();
					}
				} else {
					Yii::app()->user->setFlash('registration',
							Yii::t("UserModule.user",
								"Your registration didn't work. Please contact our System Administrator."));
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
	public function sendRegistrationEmail($user)
	{
		if(!isset($user->email))
		{
			throw new CException(Yii::t('UserModule.user', 'Email is not set when trying to send Registration Email'));	
		}

		$headers = "From: " . Yii::app()->params['adminEmail']."\r\nReply-To: ".Yii::app()->params['adminEmail'];
		$activation_url = 'http://' .
			$_SERVER['HTTP_HOST'] .
			$this->createUrl('user/activation',array(
						"activationKey" => $user->activationKey, "email" => $user->email)
					);
		mail($user->email,"You registered from " . Yii::app()->name,"Please activate your account go to $activation_url.",$headers);

		return true;
	}

	public function actionPasswordExpired()
	{
		$this->actionChangePassword($expired = true);
	}

	public function actionLogin()
	{
		$loginForm = new YumUserLogin;

		// collect user input data
		if(isset($_POST['YumUserLogin'])) {
			$loginForm->attributes=$_POST['YumUserLogin'];

			// validate user input and redirect to previous page if valid
			if($loginForm->validate()) {
				$user = YumUser::model()->findByPk(Yii::app()->user->id);
				$user->lastvisit = time();
				$user->save();

				if(isset(Yii::app()->user->returnUrl))
					$this->redirect(Yii::app()->user->returnUrl);
				else if($user->superuser)
					$this->redirect(Yii::app()->getModule('user')->returnAdminUrl);
				else if($user->isPasswordExpired())
					$this->redirect(array('passwordexpired'));
								else
					$this->redirect(Yii::app()->getModule('user')->returnUrl);
			}
		}

		// if the login Action is called from the Quick Login widget, just refresh
		// the page, otherwise render the Login Form 
		if(isset($_POST['quicklogin']))
			$this->refresh();

		$this->render('/user/login', array('model' => $loginForm));
	}

	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->controller->module->returnLogoutUrl);
	}

	/**
	 * Activation of an user account
	 */
	public function actionActivation ()
	{
		if(YumUser::activate($_GET['email'], $_GET['activationKey']))
		{
			$this->render('message', array(
						'title'=>Yum::t("User activation"),
						'content'=>Yum::t("Your account has been activated.")));
		} else {
			$this->render('message',array(
						'title'=>Yum::t("User activation"),
						'content'=>Yum::t("Incorrect activation URL.")));
		}
	}

	/**
	 * Change password
	 */
	public function actionChangepassword($expired = false) {
		if(isset($_GET['id']))
			$uid = $_GET['id'];
		else
			$uid =Yii::app()->user->id;

		$form = new YumUserChangePassword;
		if (isset(Yii::app()->user->id)) {
			if(isset($_POST['YumUserChangePassword'])) {
				$form->attributes = $_POST['YumUserChangePassword'];
				if($form->validate()) {
					$new_password = YumUser::model()->findByPk($uid);
					$new_password->password = YumUser::encrypt($form->password);
					$new_password->lastpasswordchange = time();
					$new_password->activationKey = YumUser::encrypt(microtime().$form->password);

					if($new_password->save()) {
						Yii::app()->user->setFlash('profileMessage',
								Yii::t("UserModule.user", "The new password has been saved."));
						$this->redirect(array("user/profile"));
					} else {
						Yii::app()->user->setFlash('profileMessage',
								Yii::t("UserModule.user", "There was an error saving the password."));
						$this->redirect(array('/user/profile'));
					}
				}
			}
			$this->render('changepassword', array('form'=>$form, 'expired' => $expired));
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

	public function actionAssign()
	{
		Relation::handleAjaxRequest($_POST);
	}


	public function actionProfile()
	{
		// Display my own profile:
		if(!isset($_GET['id']) || $_GET['id'] == Yii::app()->user->id)
		{
			if (Yii::app()->user->id)
			{
				$model = $this->loadUser(Yii::app()->user->id);

				$this->render('/profile/myprofile',array(
							'model'=>$model,
							'profile'=>$model->profile,
							'messages'=>$model->messages,
							));
			}
		}
		else
		{
			// Display a foreign profile:
			$model = $this->loadUser($uid = $_GET['id']);

			if($this->module->forceProtectedProfiles == true ||
					$model->profile[0]->privacy == 'protected' ||
					$model->profile[0]->privacy == 'private')
			{
				$this->render('/profile/profilenotallowed');
			}
			else
			{
				$this->render('foreignprofile',array(
							'model'=>$model,
							'profile'=>$model->profile,
							));
			}
		}
	}

	/**
	 * Edits a User profile.
	 */
	public function actionEdit()
	{
		if($this->module->readOnlyProfiles == true)
		{
			Yii::app()->user->setFlash('profileMessage',
					Yii::t("UserModule.user",
						"You are not allowed to edit your own profile. Please contact your System Administrator."));

			$this->redirect(array('profile', 'id'=>$model->id));
		}

		$model = YumUser::model()->findByPk(Yii::app()->user->id);
		$profile = $model->profile[0];

		if(isset($_POST['YumUser'])) {
			$model->attributes=$_POST['YumUser'];
			if($this->module->profileHistory == true)
				$profile = new YumProfile();

			if(isset($_POST['YumProfile'])) {
				$profile->attributes=$_POST['YumProfile'];
				$profile->timestamp = time();
				$profile->privacy = $_POST['YumProfile']['privacy'];
				$profile->user_id = $model->id;
			}
			$model->validate();
			$profile->validate();
			if(!$model->hasErrors() && !$profile->hasErrors()) {
				$model->save();
				$profile->save();
				Yii::app()->user->setFlash('profileMessage',
						Yii::t("UserModule.user", "Your changes have been saved"));
				$this->redirect(array('profile', 'id'=>$model->id));
			}
		}

		$this->render('/profile/profile-edit',array(
					'model'=>$model,
					'profile'=>$profile,
					));

	}

	/**
	 * Displays a User
	 */
	public function actionView()
	{
		$model = $this->loadUser();
		$this->render('view',array(
					'model'=>$model,
					));
	}

	/**
	 * Creates a new User.
	 */
	public function actionCreate()
	{
		$this->layout = YumWebModule::yum()->adminLayout;
		$model = new YumUser;
		$profile = new YumProfile;
		$passwordform = new YumUserChangePassword;

		// When opening a empty user creation mask, we most probably want to 
		// insert an _active_ user
		$model->status = 1;

		if(isset($_POST['YumUser'])) {
			$model->attributes=$_POST['YumUser'];

			$model->roles = Relation::retrieveValues($_POST, 'YumRole');
			$model->activationKey = YumUser::encrypt(microtime() . $model->password);
			$model->createtime=time();
			$model->lastvisit=time();

			if(isset($_POST['YumProfile']))
				$profile->attributes = $_POST['YumProfile'];
			$profile->user_id = 0;

			if(isset($_POST['YumUserChangePassword'])) {
				$passwordform->attributes = $_POST['YumUserChangePassword'];
				if($passwordform->validate())
					$model->password = YumUser::encrypt($passwordform->password);
			}

			$model->validate();
			$profile->validate();
			if(!$model->hasErrors() && !$profile->hasErrors() && !$passwordform->hasErrors()) {
				if($model->save()) {
					$profile->user_id = $model->id;
					$profile->save();
					$this->redirect(array('view', 'id'=>$model->id));
				}
			}
		}

		$this->render('create',array(
					'model' => $model,
					'passwordform' => $passwordform,
					'profile' => $profile,
					'tabularIdx' => null,
					));
	}

	public function actionUpdate()
	{
		$this->layout = YumWebModule::yum()->adminLayout;

		$model = $this->loadUser();
		$passwordform = new YumUserChangePassword();

		$changepassword = isset($_POST['change_Password']);

		// Always operate on most actual profile
		if($model->profile === false)
			$model->profile = new YumProfile();
		else if(!is_array($model->profile))
			$model->profile = array($model->profile);

		$profile = $model->profile[0];

		if(isset($_POST['YumUser'])) {
			$model->attributes = $_POST['YumUser'];

			// Assign the roles and belonging Users to the model
			if(!isset($_POST['YumUser']['YumRole']))
				$_POST['YumUser']['YumRole'] = array();

			if(!isset($_POST['YumUser']['YumUser']))
				$_POST['YumUser']['YumUser'] = array();

			$model->roles = $_POST['YumUser']['YumRole'];
			$model->users = $_POST['YumUser']['YumUser'];

			if(isset($_POST['YumProfile'])) {
				if($this->module->profileHistory == true)
					$profile = new YumProfile();

				$profile->attributes = $_POST['YumProfile'];
				$profile->user_id = $model->id;
			}

			if($changepassword) { 
				$model->validate();
				$profile->validate();
				$passwordform->attributes = $_POST['YumUserChangePassword'];
				$passwordform->validate();

				if(!$model->hasErrors() && !$profile->hasErrors() && !$passwordform->hasErrors()) {
					$model->password = YumUser::encrypt($passwordform->password);
					$model->lastpasswordchange = time();
					$model->save();
					$profile->save();
					$this->redirect(array('view','id'=>$model->id));
				}
			} else {
				$model->validate();
				$profile->validate();

				if(!$model->hasErrors() && !$profile->hasErrors()) {
					$model->save();
					$profile->save();
					$this->redirect(array('view','id'=>$model->id));
				}
			}
		}	

		$this->render('update', array(
					'model'=>$model,
					'passwordform' =>$passwordform,
					'changepassword' => $changepassword,
					'profile'=>$profile,
					'tabularIdx'=>null,
					));
	}


	/**
	 * Deletes a User, and if preserve History is deactivated, deletes all
	 * profiles of that user.
	 */
	public function actionDelete()
	{
		if(Yii::app()->user->isAdmin()) {
			$this->layout = YumWebModule::yum()->adminLayout;
			if(isset($_GET['id']) && $model = $this->loadUser($_GET['id'])) {
				if($model->id == Yii::app()->user->id) {
					Yii::app()->user->setFlash('adminMessage', 'You can not delete your own admin account');
					if(!Yii::app()->request->isAjaxRequest)
						$this->redirect(array('//user/user/admin'));
				} else
					$model->delete();	
			}
		} else {
			$this->layout = YumWebModule::yum()->layout;
			$model = $this->loadUser(Yii::app()->user->id);

			$preserveProfiles = Yii::app()->getModule('user')->preserveProfiles;
			if(isset($_POST['confirmPassword'])) {
				if($model->encrypt($_POST['confirmPassword']) == $model->password) {
					if(Yii::app()->controller->module->profileHistory == false) {
						if(is_array($model->profile) && !$preserveProfiles) {
							foreach($model->profile as $profile) {
								$profile->delete();
							}
						} else if (is_object($model->profile) && !$preserveProfiles) 
							$model->profile->delete();
					}
					$model->delete();
					$this->actionLogout();
				} else {
					Yii::app()->user->setFlash('profileMessage',
							sprintf('%s. (%s)', 
								Yii::t('UserModule.user',
									'Wrong password confirmation! Account was not deleted'),
								CHtml::link(Yii::t('UserModule.user', 'Try again'), array(
										'delete'))
								)
							);
						$this->redirect('profile');
				}
			} else {
				$this->render('confirmDeletion', array('model' => $model));
				Yii::app()->end();
			}
		}

		if(!Yii::app()->request->isAjaxRequest)
			$this->redirect(array('//user/user/admin'));
	}



	public function actionList()
	{
		$this->layout = YumWebModule::yum()->adminLayout;
		$dataProvider=new CActiveDataProvider('YumUser', array(
					'pagination'=>array(
						'pageSize'=>self::PAGE_SIZE,
						)));

		$this->render('index',array(
					'dataProvider'=>$dataProvider,
					));
	}

	public function actionAdmin()
	{
		$this->layout = YumWebModule::yum()->adminLayout;
		$model = new YumUser('search');

		if(isset($_GET['YumUser']))
			$model->attributes = $_GET['YumUser'];                                    

		$users = YumUser::model()->findAll();

		$this->render('admin',array(
					'users'=>$users,
					'model'=>$model,
					));


	}

	/**
	 * Loads the User Object instance
	 * @return YumUser
	 */
	public function loadUser($uid = 0)
	{
		if($this->_model === null)
		{
			if($uid != 0)
				$this->_model = YumUser::model()->findByPk($uid);
			elseif(isset($_GET['id']))
				$this->_model = YumUser::model()->findByPk($_GET['id']);
			if($this->_model === null)
				throw new CHttpException(404,'The requested User does not exist.');
		}
		return $this->_model;
	}

}
