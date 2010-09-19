<?php

class YumUserController extends YumController
{
	const PAGE_SIZE=10;
	public $defaultAction = 'login';
	private $_model;

	public function accessRules()
	{
		return array(
				array('allow',
					'actions'=>array('index','view','login'),
					'users'=>array('*'),
					),
				array('allow',
					'actions'=>array('profile', 'logout', 'changepassword', 'passwordexpired', 'delete'),
					'users'=>array('@'),
					),
				array('allow',
					'actions'=>array('admin','delete','create','update', 'list', 'assign', 'generateData'),
					'users'=>array(Yii::app()->user->name ),
					'expression' => 'Yii::app()->user->isAdmin()'
					),
				array('allow',
					'actions' => array('admin'),
					'expression' => "Yii::app()->user->hasUsers()",
					),
				array('allow',
					'actions' => array('admin'),
					'expression' => "Yii::app()->user->hasRoles()",
					),
				array('allow',
						'actions' => array('update'),
						'expression' => 'Yii::app()->user->hasUser($_GET[\'id\'])',
						),
				array('allow',
						'actions' => array('update'),
						'expression' => 'Yii::app()->user->hasRoleOfUser($_GET[\'id\'])',
						),
				array('deny',  // deny all other users
						'users'=>array('*'),
						),
				);
	}

	public function actionGenerateData() {
		if(isset($_POST['user_amount'])) {
			for($i = 0; $i < $_POST['user_amount']; $i++) {
				$user = new YumUser();
				$user->username = sprintf('Demo_%d_%d', rand(1, 50000), $i);
				$user->roles = array($_POST['role']);
				$user->password = 'Demopassword123';
				$user->createtime = time();
				$user->status = $_POST['status'];

				if($user->save()) {
					if(Yii::app()->getModule('user')->enableProfiles) {
						$profile = new Profile();
						$profile->user_id = $user->id;
						$profile->save();
					}
				}
			}
			echo 'Users have been generated. Password for each user is <em>Demopassword123</em>';
		}
		$this->render('generate_data');	
	}

	public function actionIndex() {
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


	public function actionPasswordExpired()
	{
		$this->actionChangePassword($expired = true);
	}

	public function actionLogin() {
		$this->layout = Yii::app()->getModule('user')->loginLayout;
		$loginForm = new YumUserLogin;

		// collect user input data
		if(isset($_POST['YumUserLogin'])) {
			$loginForm->attributes=$_POST['YumUserLogin'];

			// validate user input and redirect to previous page if valid
			if($loginForm->validate()) {
				$user = YumUser::model()->findByPk(Yii::app()->user->id);
				$user->lastvisit = time();
				$user->save();

				if($this->module->messageSystem != YumMessage::MSG_NONE
						&& count($user->messages) > 0) {
					$this->renderPartial('/messages/new_messages');
				}

				if($user->superuser) {
					$this->redirect(Yii::app()->getModule('user')->returnAdminUrl);
				} else {
					if ($user->isPasswordExpired())
						$this->redirect(array('passwordexpired'));
					else if(Yii::app()->getModule('user')->returnUrl !== '')
						$this->redirect(Yii::app()->getModule('user')->returnUrl);
					else
						$this->redirect(Yii::app()->user->returnUrl);
				}
			}

			// if the login Action is called from the Quick Login widget, just refresh
			// the page, otherwise render the Login Form 
			if(isset($_POST['quicklogin']))
				$this->refresh();

		}
		$this->render('/user/login', array('model' => $loginForm));
	}

	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->controller->module->returnLogoutUrl);
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

	public function actionProfile() {
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
		$profiles = $this->module->enableProfiles;
		$model = new YumUser;
		$profile = new YumProfile;
		$passwordform = new YumUserChangePassword;

		// When opening a empty user creation mask, we most probably want to 
		// insert an _active_ user
		$model->status = 1;

		if(isset($_POST['YumUser'])) {
			$model->attributes=$_POST['YumUser'];

			if(isset($_POST['YumUser']['YumRole']))
				$model->roles = $_POST['YumUser']['YumRole'];
			else
				$model->roles = array();

			if(isset($_POST['YumUser']['YumUser']))
				$model->users = $_POST['YumUser']['YumUser'];
			else
				$model->users = array();

			$model->activationKey = YumUser::encrypt(microtime() . $model->password);
			$model->createtime=time();
			$model->lastvisit=time();

			if($profiles) {
				if(isset($_POST['YumProfile']))
					$profile->attributes = $_POST['YumProfile'];
				$profile->user_id = 0;
			}

			if(isset($_POST['YumUserChangePassword'])) {
				$passwordform->attributes = $_POST['YumUserChangePassword'];
				if($passwordform->validate())
					$model->password = YumUser::encrypt($passwordform->password);
			}

			$model->validate();
			if($profiles) $profile->validate();
			if(!$model->hasErrors() && !$passwordform->hasErrors()) {


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
					'profile' => $profiles ? $profile : false,
					'tabularIdx' => null,
					));
	}

	public function actionUpdate()
	{
		$this->layout = YumWebModule::yum()->adminLayout;

		// determine if profiles are enabled
		$profiles = Yii::app()->getModule('user')->enableProfiles;
		$changepassword = isset($_POST['change_password']);

		$model = $this->loadUser();
		$passwordform = new YumUserChangePassword();


		if($profiles) {
			// Always operate on most actual profile
			if($model->profile === false)
				$model->profile = new YumProfile();

			if(!is_array($model->profile))
				$model->profile = array($model->profile);

			$profile = $model->profile[0];
		}

		if(isset($_POST['YumUser'])) {
			$model->attributes = $_POST['YumUser'];

			// Assign the roles and belonging Users to the model
			if(!isset($_POST['YumUser']['YumRole']))
				$_POST['YumUser']['YumRole'] = array();

			if(!isset($_POST['YumUser']['YumUser']))
				$_POST['YumUser']['YumUser'] = array();

			$model->roles = $_POST['YumUser']['YumRole'];
			$model->users = $_POST['YumUser']['YumUser'];

			if($profiles) {
				if(isset($_POST['YumProfile'])) {
					if($this->module->profileHistory)
						$profile = new YumProfile();

					$profile->attributes = $_POST['YumProfile'];
					$profile->user_id = $model->id;
				}
			}

			$errors = false;

			if($changepassword) { 
				$passwordform->attributes = $_POST['YumUserChangePassword'];
				$passwordform->validate();

				if(!$passwordform->hasErrors()) {
					$model->password = YumUser::encrypt($passwordform->password);
					$model->lastpasswordchange = time();
					$model->save();
				} else
				 $errors = true;
			} 
				$model->validate();

			if($profiles) {
				$profile->validate();

				if(!$model->hasErrors() && !$profile->hasErrors()) {
					$model->save();
					$profile->save();
					$this->redirect(array('view','id'=>$model->id));
				}
			} else {
				if($model->save() && !$errors)
					$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('update', array(
					'model'=>$model,
					'passwordform' =>$passwordform,
					'changepassword' => $changepassword,
					'profile'=>$profiles ? $profile : false,
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
					'criteria' => array('condition' => 'status = 1'),
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

		if(Yii::app()->user->isAdmin()) {
			$model = new YumUser('search');

			if(isset($_GET['YumUser']))
				$model->attributes = $_GET['YumUser'];                                    



			$this->render('admin', array('model'=>$model));
		} else {
			$model = YumUser::model()->findByPk(Yii::app()->user->id);
			$this->render('restricted_admin', array('users'=>$model->getAdministerableUsers()));
		}
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
