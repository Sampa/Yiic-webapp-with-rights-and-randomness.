<?php

Yii::import('application.modules.user.controllers.YumController');
class YumUserController extends YumController {
	public $defaultAction = 'login';

	public function accessRules() {
		return array(
				array('allow',
					'actions'=>array('index', 'view', 'login'),
					'users'=>array('*'),
					),
				array('allow',
					'actions'=>array('admin','delete','create','update', 'list', 'assign', 'generateData'),
					'users'=>array(Yii::app()->user->name ),
					'expression' => 'Yii::app()->user->isAdmin()'
					),
				array('deny',  // deny all other users
						'users'=>array('*'),
						),
				);
	}

	public function actionIndex() {
		// If the user is not logged in, so we redirect to the actionLogin,
		// which will render the login Form

		if(Yii::app()->user->isGuest)
			$this->actionLogin();
		else
			$this->actionList();
	}

	public function actionLogin() {
		// Do not show the login form if a session expires but a ajax request
		// is still generated
		if(Yii::app()->user->isGuest && Yii::app()->request->isAjaxRequest)
			return false;
		$this->redirect(array('/user/auth'));
	}

	public function actionLogout() {
		$this->redirect(array('//user/auth/logout'));
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
	public function actionCreate() {
		$model = new YumUser;

		// When opening a empty user creation mask, we most probably want to
		// insert an _active_ user
		if(!isset($model->status))
			$model->status = 1;

		if(isset($_POST['YumUser'])) {
			$model->attributes=$_POST['YumUser'];

			$model->activationKey = YumUser::encrypt(microtime() . $model->password);

			if($model->save()) 
				$this->redirect(array('view', 'id'=>$model->id));
		}

		$this->render('create',array(
					'model' => $model,
					'passwordform' => $passwordform,
					'profile' => $profile,
					));
	}

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
