<?php
class YumFriendshipController extends YumController {
	private $_model;
	// make sure that friendship is enabled in the configuration
	public function beforeAction($action) {
		if(!Yum::module()->enableFriendship) 
			return false;
		return(parent::beforeAction($action));
	}
	
	public function actionIndex()
	{
		if(isset($_POST['YumFriendship']['friendship_id']) )
		{
			$friendship=YumFriendship::model()->findByPK($_POST['YumFriendship']['friendship_id']);
			if($friendship->inviter_id == Yii::app()->user->id || $friendship->friend_id == Yii::app()->user->id)
				if(isset($_POST['YumFriendship']['add_request']))
				{
					$friendship->status = 2;
					$friendship->save();
				}elseif(isset($_POST['YumFriendship']['deny_request']))
				{
					$friendship->status = 3;
					$friendship->save();
				}elseif(isset($_POST['YumFriendship']['ignore_request']))
				{
					$friendship->status = 0;
					$friendship->save();
				}elseif(isset($_POST['YumFriendship']['cancel_request']) || isset($_POST['YumFriendship']['remove_friend']))
				{
			$friendship->delete();
			}
		}
		$user = YumUser::model()->findByPk(Yii::app()->user->id);
		$myfriends=$user->getFriendships();
		$this->render('myfriends', array('friends' => $myfriends,));
	}

	public function actionAdmin() {
		$user = YumUser::model()->findByPk(Yii::app()->user->id);
		if(Yii::app()->user->isAdmin()) {
			$model = new YumUser('search');

			if(isset($_GET['YumUser']))
				$model->attributes = $_GET['YumUser'];                                    



			$this->render('admin', array('model'=>$model));
		} else {
			$model = YumUser::model()->findByPk(Yii::app()->user->id);
			$this->render('restricted_admin', array('users'=>$model->getAdministerableUsers()));
		}

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


		//$this->render('admin', array('friendships' => $user->friendships));
	}

	public function actionfriendAdmin() {
		if(Yii::app()->user->isAdmin()) {
			$user=YumUser::model()->findByPK($_GET['id']);
			$friendships=$user->getFriendships();
			
			$friendships=new CActiveDataProvider('YumFriendship', array(
    'criteria'=>array(
        'condition'=>'inviter_id = :user_id || friend_id = :user_id',
        'params'=>array(':user_id'=>$user->id),
        'order'=>'status ASC',
    ),
    'pagination'=>array(
        'pageSize'=>20,
    ),
));


			$this->render('friendedit', array('friends' => $friendships,'user'=>$user));
			
		}else{
			
		}
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

	public function ActionConfirmFriendship($id,$key)
	{
	$friendship=YumFriendship::model()->findByPK($id);
	if($friendship->friend_id == $key)
	{
		$friendship->acceptFriendship();
		$verified=true;
	}else{
		$verified=false;
	}
	return $verified;
	}
	
	public function actionView()
	{
		$model = YumFriendship::model()->findByPK($_GET['id']);
		$model->acknowledgetime =date('M/j/y g:i',$model->acknowledgetime);
		$model->requesttime =date('M/j/y g:i',$model->requesttime);
		$model->updatetime =date('M/j/y g:i',$model->updatetime);
		$this->render('view',array(
					'model'=>$model,
					));
	}
	
	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadModel()->delete();

			// if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
			if(!isset($_GET['ajax']))
				$this->redirect(array('index'));
		}
		else
			throw new CHttpException(400,Yii::t('App','Invalid request. Please do not repeat this request again.'));
	}
	
	public function actionUpdate()
	{
		$model=$this->loadModel();

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(Yii::app()->request->isPostRequest)
		{
			$model->attributes=$_POST['YumFriendship'];
			if($model->save())
				$this->redirect(array('view','id'=>$model->id));
		}

		$this->render('update',array(
			'model'=>$model,
		));
	}
	
	public function loadModel()
	{
		if($this->_model===null)
		{
			if(isset($_GET['id']))
				$this->_model=YumFriendship::model()->findbyPk($_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,Yii::t('App','The requested page does not exist.'));
		}
		return $this->_model;
	}
	
	public static function invitationLink($inviter, $invited) {
		if($inviter === $invited)
			return false;
		if(!is_object($inviter))
			$inviter = YumUser::model()->findByPk($inviter);
		if(!is_object($invited))
			$invited = YumUser::model()->findByPk($invited);

		$friends = $inviter->getFriends(true);
		if($friends && $friends[0] != NULL)
			foreach($friends as $friend) 
				if($friend->id == $invited->id)
				return false; // already friends, rejected or request pending

			return CHtml::link(Yum::t('Add as a friend'), array(
						'friendship/invite', 'user_id' => $invited->id));
	}
}

?>
