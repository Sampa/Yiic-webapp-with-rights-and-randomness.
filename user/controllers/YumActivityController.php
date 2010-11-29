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
				'failed_login_attempt',
				'profile_set_active',
				'settings_profile_created',
				'settings_updated',
				'settings_profile_removed',
				'field_group_created',
				'field_group_updated',
				'field_group_removed',
				'profile_field_created',
				'profile_field_updated',
				'profile_field_removed',
				'role_created',
				'role_updated',
				'role_removed',
				'text_settings_created',
				'text_settings_updated',
				'text_settings_removed',
				'user_generated',
				'user_created',
				'user_updated',
				'user_removed');
		if(!in_array($action, $possible_actions) || !Yum::module()->enableLogging)
			return false;

		$activity = new YumActivity;

		if(!(is_object($user) && $user instanceof YumUser))
			$user = YumUser::model()->findByPk($user);

		$activity->user_id = $user->id;
		$activity->timestamp = time();
		$activity->action = $action; 
		$activity->remote_addr = $_SERVER['REMOTE_ADDR'];

		$activity->save();
		
		switch ($action) 
		{
			case 'login':
				Yii::log(Yum::t('User {username} successfully logged in', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'logout':
					Yii::log(Yum::t('User {username} successfully logged off', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'register' :
					Yii::log(Yum::t('User {username} registered at the adminstration form ', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
				case 'recovery':
					Yii::log(Yum::t('User {username} requested a new password ', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'failed_login_attempt' :
					Yii::log(Yum::t('Wrong password for {username} entered', array(
							'{username}' => $user->username)),
					'warning',
					'modules.user.controllers.YumUserController');
					break;
				case 'profile_set_active' :
					Yii::log(Yum::t('User {username} set a new main profile active', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'settings_profile_created':
					Yii::log(Yum::t('User {username} created a new main profile', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'settings_updated':
					Yii::log(Yum::t('User {username} main profile settings updated', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'settings_profile_removed':
					Yii::log(Yum::t('User {username} main profile settings removed', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'field_group_created':
					Yii::log(Yum::t('User {username} created a new field group', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'field_group_updated':
					Yii::log(Yum::t('User {username} changed a field group', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'field_group_removed':
					Yii::log(Yum::t('User {username} removed a field group', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'profile_field_created':
					Yii::log(Yum::t('User {username} created a profile field', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'profile_field_updated':
					Yii::log(Yum::t('User {username} removed a profile field', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'profile_field_removed':
					Yii::log(Yum::t('User {username} removed a field group', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'role_created':
					Yii::log(Yum::t('User {username} created a role', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'role_updated':
					Yii::log(Yum::t('User {username} updated a role', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'role_removed':
					Yii::log(Yum::t('User {username} removed a field group', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'text_settings_created':
					Yii::log(Yum::t('User {username} created new text settings', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'text_settings_updated':
					Yii::log(Yum::t('User {username} updated text settings', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'text_settings_removed':
					Yii::log(Yum::t('User {username} removed text setting', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'user_generated':
					Yii::log(Yum::t('User {username} auto-generated user(s)', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'user_created':
					Yii::log(Yum::t('User {username} created a new user', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'user_updated':
					Yii::log(Yum::t('User {username} updated a user', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				case 'user_removed':
					Yii::log(Yum::t('User {username} removed a user', array(
							'{username}' => $user->username)),
					'info',
					'modules.user.controllers.YumUserController');
					break;
				
		}

}		
			
			
}
