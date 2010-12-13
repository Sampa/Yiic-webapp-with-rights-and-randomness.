<?php

Yii::import('application.modules.user.controllers.YumController');

class YumProfileController extends YumController
{
	const PAGE_SIZE=10;
	private $_model;

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

	public function actionEdit() {
		if(Yum::module()->readOnlyProfiles) {
			Yii::app()->user->setFlash('profileMessage',
					Yum::t('You are not allowed to edit your own profile.
						Please contact your System Administrator.'));

				$this->redirect(array('/user/user/profile', 'id'=>$model->id));
		}

		$model = YumUser::model()->findByPk(Yii::app()->user->id);
		if(isset($model->profile[0])) 
			$profile = $model->profile[0];
		else
			$profile = new YumProfile();

		if(isset($_POST['YumUser'])) {
			$model->attributes=$_POST['YumUser'];
			if(Yum::module()->profileHistory == true)
				$profile = new YumProfile();

			if(isset($_POST['YumProfile'])) {
				$profile->attributes=$_POST['YumProfile'];
				$profile->timestamp = time();
				$profile->user_id = $model->id;
			}
			$model->validate();
			$profile->validate();
			$settings=Yumsettings::model()->findByPK(1);
			$notifyemailchange=$settings->notifyemailchange;
			if(!$model->hasErrors() && !$profile->hasErrors()) {
				
				if($model->profile[0]->email != $profile->email && isset($notifyemailchange))
				{
					//send confirmation email
					switch ($notifyemailchange)
					{
						case 'oldemail':
						$this->sendAlertEmail($model->profile[0]->email,Yum::t('Email Change Requested'),'A request to change your email address to '.$profile->email.' was made from '.CHttpRequest::getUserHostAddress(). ' on ' . date('m/j/y g:ia'));
						break;
						case'newemail':
						$this->sendAlertEmail($profile->email,Yum::t('Email Change Requested'),'A request to change your email address from '.$model->profile[0]->email.' was made from '.CHttpRequest::getUserHostAddress(). ' on ' . date('m/j/y g:ia'));
						break;
					}
				
				$profile->is_active=0;
				}
				$model->save();
				$profile->save();
				Yii::app()->user->setFlash('profileMessage',
						Yum::t('Your changes have been saved'));
				$this->redirect(array('/user/user/profile', 'id'=>$model->id));
			}
		}

		$this->render('/profile/profile-edit',array(
					'model'=>$model,
					'profile'=>$profile,
					));

	}

	public function actionVisits() {
		$this->layout = Yum::module()->adminLayout;

		$this->render('visits',array(
			'model'=>new YumProfileVisit(),
		));

	}

	public function actionView() {
		if(!isset($_GET['id']))
			$_GET['id'] = Yii::app()->user->id;

		$this->layout = Yum::module()->profileLayout;
		$view = Yum::module()->profileView;

		$model = YumUser::model()->findByPk($_GET['id']);
		$friends = $model->getFriends();

		$this->render($view, array(
					'model' => $model,
					'friends'=>$friends));
		$this->updateVisitor(Yii::app()->user->id, $model->id);
	}

	public function updateVisitor($visitor_id, $visited_id) {
		// Visiting my own profile doesn't count as visit
		if($visitor_id == $visited_id)
			return true;

		$visit = YumProfileVisit::model()->find('visitor_id = :visitor_id and visited_id = :visited_id', array(
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
				$this->redirect(array('admin'));
			}
		}

		$this->render('create',array( 'model'=>$model ));
	}

	public function actionUpdate() {
		if(Yii::app()->user->isAdmin()) 
			$this->layout = Yum::module()->adminLayout;
		else
			$this->layout = Yum::module()->layout;

		if(!isset($_GET['id'])) {
			$profile = YumProfile::model()->find('user_id = ' . Yii::app()->user->id);
			$_GET['id'] = $profile->profile_id;
		}

		$model=$this->loadModel();
		if(isset($_POST['YumProfile']))
		{
			$model->attributes=$_POST['YumProfile'];
			
			if($model->save())
				$this->redirect(array('admin'));
		}

		$this->render('/user/update',array(
					'model'=>$model->user,
					'profile' => $model,
					'passwordform' => new YumUserChangePassword(),
					));
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
	
	/**
	 * this sends an email alerting the user that some profile setting has changed
	 * @return:null
	 */
	public function sendAlertEmail($email,$subject=null,$message=null)
	{
		$headers = "From: " . Yii::app()->controller->module->recoveryEmail ."\r\nReply-To: ".Yii::app()->params['adminEmail'];
		$msgheader = $subject;
		$msgbody = $message;
		if(Yum::module()->mailer == 'swift') {
					$sm = Yii::app()->swiftMailer;
					$mailer = $sm->mailer($sm->mailTransport());
					$message = $sm->newMessage($msgheader)   
						->setFrom(Yii::app()->controller->module->recoveryEmail)
						->setTo($email)
						->setBody($msgbody);                                                    
					$sent=$mailer->send($message);
				} else {
					$sent=mail($email, $msgheader, $msgbody, $headers);
				}
	}
	
	/**
	 * @return YumProfileField
	 */
	public function loadModel()
	{
		if($this->_model===null)
		{
			if(isset($_GET['id']))
				$this->_model=YumProfile::model()->findbyPk($_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}
}
