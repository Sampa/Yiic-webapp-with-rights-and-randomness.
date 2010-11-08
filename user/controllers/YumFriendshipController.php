<?php
class YumFriendshipController extends YumController {
	// make sure that friendship is enabled in the configuration
	public function beforeAction($action) {
		if(!Yum::module()->enableFriendship) 
			return false;
		return(parent::beforeAction($action));
	}

	public function actionAdmin() {
		$user = YumUser::model()->findByPk(Yii::app()->user->id);

/*		$friendships = new CArrayDataProvider(
				$user->getFriendships(), array(
					'id' => 'friends',
					'keyField' => 'friend_id',
					'sort' => array('attributes' => array('friend_id', 'status')
					)));

		$this->render('admin', array(
					'friendships' => $friendships,
					'sortableAttributes' => array('friend_id' => 'Friend', 'status')
					)); */


		$this->render('admin', array('friendships' => $user->friendships));
	}

	public function actionInvite() {
		if(isset($_GET['user_id']))
			$user_id = $_GET['user_id'];

		if(isset($_POST['user_id']))
			$user_id = $_POST['user_id'];

		if(!isset($user_id))
			return false;

		if(isset($_POST['message']) && isset($user_id)) {
			$friendship = new YumFriendship;
			if($friendship->requestFriendship(Yii::app()->user->id,
						$_POST['user_id'],
						$_POST['message'])) {
				$this->render('success', array('friendship' => $friendship));
				Yii::app()->end();
			}
		} 
		$this->render('invitation', array(
					'inviter' => YumUser::model()->findByPk(Yii::app()->user->id),
					'invited' => YumUser::model()->findByPk($user_id),
					'friendship' => isset($friendship) ? $friendship : null,
					));
	}

	public static function invitationLink($inviter, $invited) {
		if($inviter === $invited)
			return false;
		if(!is_object($inviter))
			$inviter = YumUser::model()->findByPk($inviter);
		if(!is_object($invited))
			$invited = YumUser::model()->findByPk($invited);

		foreach($inviter->getFriends(true) as $friend) 
			if($friend->id == $invited->id)
				return false; // already friends, rejected or request pending

			return CHtml::link(Yum::t('Add as a friend'), array(
						'friendship/invite', 'user_id' => $invited->id));
	}
}

?>
