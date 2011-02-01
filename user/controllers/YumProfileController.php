<?php

Yii::import('application.modules.user.controllers.YumController');

class YumProfileController extends YumController {
	const PAGE_SIZE=10;

	public function accessRules()
	{
		return array(
			array('allow',
				'actions'=>array('index', 'create', 'admin','delete', 'visits'),
				'expression' => 'Yii::app()->user->isAdmin()'
				),
			array('allow',
				'actions'=>array('view', 'update', 'edit'),
				'users' => array('@'),
				),

			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionUpdate($id = null) {
		if(Yum::module()->readOnlyProfiles) {
			Yum::setFlash('You are not allowed to edit your own profile.
					Please contact the System administrator');

			$this->redirect(array('/user/user/profile', 'id'=>$model->id));
		}

		if(!$id)
			$id = Yii::app()->user->data()->id;

		$user = YumUser::model()->findByPk($id);
		$profile = $user->profile;

		if(isset($_POST['YumUser'])) {
			$user->attributes=$_POST['YumUser'];

			if(isset($_POST['YumProfile'])) {
				$profile->attributes = $_POST['YumProfile'];
				$profile->timestamp = time();
				$profile->user_id = $user->id;
			}
			$user->validate();
			$profile->validate();

			if(!$user->hasErrors() && !$profile->hasErrors()) {
				$currentProfile = $profile;
				if($user->save() && $profile->save()) {
					$this->sendNotifyEmail($currentProfile, $profile);
					Yum::setFlash('Your changes have been saved');
					$this->redirect(array('/user/user/profile', 'id'=>$user->id));
				}
			}
		}

		$this->render(Yum::module()->profileEditView,array(
					'user'=>$user,
					'profile'=>$profile,
					));

	}

	public function sendNotifyEmail($currentProfile, $newProfile) {
		if($currentProfile->email != $newProfile->email 
				&& Yum::module()->notifyEmailChange) 
			YumMailer::send($currentProfile->email, Yum::t('Email address changed'),
					Yum::t('A email address has been changed from {oldemail} to {newemail} at {server} on {date}.', array(
						'{oldemail}' => $currentProfile->email,
						'{newemail}' => $newProfile->email,
						'{server}' => CHttpRequest::getUserHostAddress(),
						'{date}' => date(Yum::module()->dateTimeFormat))));
	}

	public function actionVisits() {
		$this->layout = Yum::module()->adminLayout;

		$this->render('visits',array(
			'model'=>new YumProfileVisit(),
		));

	}

	public function beforeAction($action) {
		$this->layout = Yum::module()->profileLayout;
		return parent::beforeAction($action);
	}

	public function actionView($id = null) {
		// If no profile id is provided, take myself
		if(!$id)
			$id = Yii::app()->user->id;

		if(!Yum::module()->profilesViewableByGuests)
			if(Yii::app()->user->isGuest)
				throw new CHttpException(403);

		$view = Yum::module()->profileView;

		if(is_numeric($id))
			$model = YumUser::model()->findByPk($id);
		else if(is_string($id))
			$model = YumUser::model()->find("username = '{$id}'");

		$this->updateVisitor(Yii::app()->user->id, $id);

		if(Yii::app()->request->isAjaxRequest)
			$this->renderPartial($view, array(
						'model' => $model));
		else
			$this->render($view, array(
						'model' => $model));

	}

	public function updateVisitor($visitor_id, $visited_id) {
		// If the user does not want to log his profile visits, cancel here
		if(isset(Yii::app()->user->data()->privacy) &&
				!Yii::app()->user->data()->privacy->log_profile_visits)
			return false;
			
		// Visiting my own profile doesn't count as visit
		if($visitor_id == $visited_id)
			return true;

		$visit = YumProfileVisit::model()->find(
				'visitor_id = :visitor_id and visited_id = :visited_id', array(
					':visitor_id' => $visitor_id,
					':visited_id' => $visited_id));
		if($visit) {
			$visit->save();
		} else {
			$visit = new YumProfileVisit();
			$visit->visitor_id = $visitor_id;
			$visit->visited_id = $visited_id;
			$visit->save();
		}
	}

	public function actionCreate()
	{
		$this->layout = Yum::module()->adminLayout;
		$model = new YumProfile;

		if(isset($_POST['YumProfile'])) {
			$model->attributes=$_POST['YumProfile'];

			if($model->validate())
			{
				$model->save();
				$this->redirect(array('view', 'id' => $model->id));
			}
		}

		$this->render('create',array( 'model'=>$model ));
	}

	public function actionDelete()
	{
		$this->layout = Yum::module()->adminLayout;

		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$model = $this->loadModel();
			$model->delete();

			if(!isset($_POST['ajax']))
				$this->redirect(array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionIndex()
	{
		if(Yii::app()->user->isAdmin())
			$this->actionAdmin();
		else
			$this->redirect('view');
	}

	public function actionAdmin()
	{
		$this->layout = Yum::module()->adminLayout;
		$model= new YumProfile;
		$dataProvider=new CActiveDataProvider('YumProfile', array(
			'pagination'=>array(
				'pageSize'=>self::PAGE_SIZE,
			),
			'sort'=>array(
				'defaultOrder'=>'profile_id',
			),
		));

		$this->render('admin',array(
			'dataProvider'=>$dataProvider,'model'=>$model,
		));
	}
}
