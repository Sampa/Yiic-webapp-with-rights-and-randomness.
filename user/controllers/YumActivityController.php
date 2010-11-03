<?php
class YumActivityController extends YumController {

	public function actionIndex()
	{
		$dataProvider=new CActiveDataProvider('YumActivity');
		$this->render('index',array(
			'dataProvider'=>$dataProvider,
		));
	}

	public static function logActivity($user, $action) {
		$possible_actions = array('login',
				'logout',
				'register',
				'recovery',
				'failed_login_attempt');
		if(!in_array($action, $possible_actions) || !Yum::module()->enableLogging)
			return false;

		$activity = new YumActivity;

		if(!(is_object($user) && $user instanceof YumUser))
			$user = YumUser::model()->findByPk($user);

		$activity->user_id = $user->id;
		$activity->timestamp = time();
		$activity->action = $action; 
		$activity->save();

		if($action == 'login')
			Yii::log(Yum::t('User {username} successfully logged in', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
		else if($action == 'logout')
			Yii::log(Yum::t('User {username} successfully logged off', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
		else if($action == 'register')
			Yii::log(Yum::t('User {username} registered at the adminstration form ', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
		else if($action == 'recovery')
			Yii::log(Yum::t('User {username} requested a new password ', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
		else if($action == 'failed_login_attempt')
			Yii::log(Yum::t('Wrong password for {username} entered', array(
							'{username}' => $user->username)),
					'warning',
					'modules.user.controllers.YumUserController');




	}

}
