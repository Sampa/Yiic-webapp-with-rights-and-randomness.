<?php

class UserController extends Controller
{
	const PAGE_SIZE=20;

	private $_model;


	public function beforeAction() 
	{
		$this->layout = Yii::app()->controller->module->layout;
		return true;
	}

	public function filters()
	{
		return array(
			'accessControl',
		);
	}

	public function accessRules()
	{
		return array(
				array('allow', 
					'actions'=>array('index','view','registration','captcha','login', 'recovery', 'activation'),
					'users'=>array('*'),
				     ),
				array('allow',
					'actions'=>array('profile', 'edit', 'logout', 'changepassword'),
					'users'=>array('@'),
				     ),
				array('allow', 
					'actions'=>array('admin','delete','create','update','assign', 'revoke'),
					'users'=>User::getAdmins(),
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

	/**
	 * Registration user
	 */
	public function actionRegistration() 
	{
		$model = new RegistrationForm;
		$profile=new Profile;
		if ($uid = Yii::app()->user->id) 
		{
			$this->redirect(Yii::app()->homeUrl);
		} 
		else 
		{
			if(isset($_POST['RegistrationForm'])) 
			{
				$model->attributes=$_POST['RegistrationForm'];
				$profile->attributes=$_POST['Profile'];
				if($model->validate()&&$profile->validate())
				{
					$sourcePassword = $model->password;
					$model->password = Yii::app()->User->encrypting($model->password);
					$model->verifyPassword = Yii::app()->User->encrypting($model->verifyPassword);
					$model->activkey = Yii::app()->User->encrypting(microtime().$model->password);
					$model->createtime = time();
					$model->lastvisit = ((Yii::app()->User->autoLogin && Yii::app()->User->loginNotActiv) ? time() : 0);
					$model->superuser = 0;
					$model->status = 0;

					if ($model->save()) 
					{
						//$model->save();
						$profile->user_id = $model->id;
						$profile->save();
						$headers="From: ".Yii::app()->params['adminEmail']."\r\nReply-To: ".Yii::app()->params['adminEmail'];
						$activation_url = 'http://' .
							$_SERVER['HTTP_HOST'] .
							$this->createUrl('user/activation',array(
								"activkey" => $model->activkey, "email" => $model->email)
							);
						mail($model->email,"You registered from " . Yii::app()->name,"Please activate you account go to $activation_url.",$headers);
						if (Yii::app()->User->loginNotActiv) 
						{
							if (Yii::app()->User->autoLogin) 
							{
								$identity = new UserIdentity($model->username,$sourcePassword);
								$identity->authenticate();
								Yii::app()->user->login($identity, 0);
								$this->redirect(Yii::app()->User->returnUrl);
							} 
							else 
							{
								Yii::app()->user->setFlash('registration',Yii::t("user", "Thank you for your registration. Please check your email or login."));
								$this->refresh();
							}
						} 
						else 
						{
							Yii::app()->user->setFlash('registration',Yii::t("user", "Thank you for your registration. Please check your email."));
							$this->refresh();
						}
					}
				}
			}
			$this->render('/user/registration',array('form'=>$model,'profile'=>$profile));
		}
	}


	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$model=new UserLogin;
		// collect user input data
		if(isset($_POST['UserLogin']))
		{
			$model->attributes=$_POST['UserLogin'];
			// validate user input and redirect to previous page if valid
			if($model->validate()) 
			{
				$lastVisit = User::model()->findByPk(Yii::app()->user->id);
				$lastVisit->lastvisit = time();
				$lastVisit->save();
				$this->redirect(Yii::app()->User->returnUrl);
			}
		}
		// display the login form
		$this->render('/user/login',array('model'=>$model,));
	}

	/**
	 * Logout the current user and redirect to returnLogoutUrl.
	 */
	public function actionLogout()
	{
		Yii::app()->user->logout();
		$this->redirect(Yii::app()->User->returnLogoutUrl);
	}

	/**
	 * Activation of an user account
	 */
	public function actionActivation () {
		$email = $_GET['email'];
		$activkey = $_GET['activkey'];
		if ($email&&$activkey) {
			$find = User::model()->findByAttributes(array('email'=>$email));
			if ($find->status) 
			{
				$this->render('/user/message',array('title'=>Yii::t("user", "User activation"),'content'=>Yii::t("user", "Your account has been activated.")));
			} elseif($find->activkey==$activkey) 
			{
				$find->activkey = Yii::app()->User->encrypting(microtime());
				$find->status = 1;
				$find->save();
				$this->render('/user/message',array('title'=>Yii::t("user", "User activation"),'content'=>Yii::t("user", "Your account has been activated.")));
			} else 
			{
				$this->render('/user/message',array('title'=>Yii::t("user", "User activation"),'content'=>Yii::t("user", "Incorrect activation URL.")));
			}
		} else 
			{
				$this->render('/user/message',array('title'=>Yii::t("user", "User activation"),'content'=>Yii::t("user", "Incorrect activation URL.")));
			}
	}

	/**
	 * Change password
	 */
	public function actionChangepassword() 
	{
		$form = new UserChangePassword;
		if ($uid = Yii::app()->user->id) 
		{
			if(isset($_POST['UserChangePassword'])) 
			{
				$form->attributes=$_POST['UserChangePassword'];
				if($form->validate()) 
				{
					$new_password = User::model()->findByPk(Yii::app()->user->id);
					$new_password->password = Yii::app()->User->encrypting($form->password);
					$new_password->activkey=Yii::app()->User->encrypting(microtime().$form->password);
					$new_password->save();
					Yii::app()->user->setFlash('profileMessage',Yii::t("user", "Your new password has been saved."));
					$this->redirect(array("user/profile"));
				}
			} 
			$this->render('/user/changepassword',array('form'=>$form));
		}
	}


	/**
	 * Recovery of a password
	 */
	public function actionRecovery () {
		$form = new UserRecoveryForm;
		if ($uid = Yii::app()->user->id) 
		{
			$this->redirect(Yii::app()->User->returnUrl);
		} 
		else 
		{
			$email = $_GET['email'];
			$activkey = $_GET['activkey'];
			if ($email && $activkey) {
				$form2 = new UserChangePassword;
				$find = User::model()->findByAttributes(array('email'=>$email));
				if($find->activkey==$activkey) {
					if(isset($_POST['UserChangePassword'])) {
						$form2->attributes=$_POST['UserChangePassword'];
						if($form2->validate()) {
							$find->password = Yii::app()->User->encrypting($form2->password);
							$find->activkey=Yii::app()->User->encrypting(microtime().$form2->password);
							$find->save();
							Yii::app()->user->setFlash('loginMessage',Yii::t("user", "Your new password has been saved."));
							$this->redirect(array("user/login"));
						}
					} 
					$this->render('/user/changepassword',array('form'=>$form2));
				} else {
					Yii::app()->user->setFlash('recoveryMessage',Yii::t("user", "Incorrect recovery link."));
					$this->redirect('http://' . $_SERVER['HTTP_HOST'].$this->createUrl('user/recovery'));
				}
			} else {
				if(isset($_POST['UserRecoveryForm'])) {
					$form->attributes=$_POST['UserRecoveryForm'];
					if($form->validate()) {
						$user = User::model()->findbyPk($form->user_id);
						$headers="From: ".Yii::app()->params['adminEmail']."\r\nReply-To: ".Yii::app()->params['adminEmail'];
						$activation_url = 'http://' . $_SERVER['HTTP_HOST'].$this->createUrl('user/recovery',array("activkey" => $user->activkey, "email" => $user->email));
						mail($user->email,"You have requested to be reset. To receive a new password, go to $activation_url.",$headers);
						Yii::app()->user->setFlash('resetPwMessage',Yii::t("user", "Instructions have been sent to you. Please check your eMail."));
						$this->refresh();
					}
				}
				$this->render('/user/recovery',array('form'=>$form));
			}
		}
	}

	public function actionProfile()
	{
		// Display my own profile:
		if(!isset($_GET['id'])) {
			if (Yii::app()->user->id) {
				$model = $this->loadUser($uid = Yii::app()->user->id);
				$this->render('/user/myprofile',array(
					'model'=>$model,
					'profile'=>$model->profile,
				));
			}
		} 
		else 
		{ // Display a foreign profile:
			$model = $this->loadUser($uid = $_GET['id']);
			$this->render('/user/foreignprofile',array(
				'model'=>$model,
				'profile'=>$model->profile,
			));
		}
	}

	public function actionAssign() 
	{
		Relation::handleAjaxRequest($_POST);
	}

	public function actionEdit()
	{
		$model=User::model()->findByPk(Yii::app()->user->id);
		if(!$profile=$model->profile)
			$profile = new Profile();

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$profile->attributes=$_POST['Profile'];

			if($model->validate()&&$profile->validate()) {
				$model->save();
				$profile->save();
				Yii::app()->user->setFlash('profileMessage',Yii::t("user", "Changes are saved."));
				$this->redirect(array('profile','id'=>$model->id));
			}
		}

		$this->render('/user/profile-edit',array(
			'model'=>$model,
			'profile'=>$profile,
		));
	}

	public function actionView()
	{
		$model = $this->loadModel();
		$this->render('/user/view',array(
			'model'=>$model,
		));
	}

	public function actionCreate()
	{
		$model=new User;
		$profile=new Profile;
		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$model->activkey=Yii::app()->User->encrypting(microtime().$model->password);
			$model->createtime=time();
			$model->lastvisit=time();
			$profile->attributes=$_POST['Profile'];
			$profile->user_id=0;
			if($model->validate()&&$profile->validate()) {
				$model->password=Yii::app()->User->encrypting($model->password);
				if($model->save()) {
					$profile->user_id=$model->id;
					$profile->save();
				}
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('/user/create',array(
			'model'=>$model,
			'profile'=>$profile,
		));
	}

	public function actionUpdate()
	{
		$model=$this->loadModel();
		if(!$profile=$model->profile) 
			$profile = new Profile();

		if(isset($_POST['User']))
		{
			$model->attributes=$_POST['User'];
			$model->roles = $_POST['User']['Role'];
			$profile->attributes=$_POST['Profile'];

			if($model->validate() && $profile->validate()) {
				$old_password = User::model()->findByPk($model->id);
				if ($old_password->password!=$model->password) {
					$model->password=Yii::app()->User->encrypting($model->password);
					$model->activkey=Yii::app()->User->encrypting(microtime().$model->password);
				}
				$model->save();
				$profile->save();
				$this->redirect(array('view','id'=>$model->id));
			}
		}

		$this->render('/user/update',array(
			'model'=>$model,
			'profile'=>$profile,
		));
	}


	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			$model = $this->loadModel();
			$model->delete();
			if(!isset($_POST['ajax']))
				$this->redirect(array('index'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('User', array(
			'pagination'=>array(
				'pageSize'=>self::PAGE_SIZE,
			),
		));

		$this->render('/user/index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	public function actionAdmin()
	{
		$dataProvider=new CActiveDataProvider('User', array(
			'pagination'=>array(
				'pageSize'=>self::PAGE_SIZE,
			),
		));

		$this->render('/user/admin',array(
			'dataProvider'=>$dataProvider,
		));
	}

	public function loadModel()
	{
		if($this->_model===null)
		{
			if(isset($_GET['id']))
				$this->_model=User::model()->findbyPk($_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}


	public function loadUser($id=null)
	{
		if($this->_model===null)
		{
			if($id!==null || isset($_GET['id']))
				$this->_model=User::model()->findbyPk($id!==null ? $id : $_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}
}
