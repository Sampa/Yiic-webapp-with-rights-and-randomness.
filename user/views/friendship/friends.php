<?php
if(isset($model->profile[0]) && $model->profile[0]->show_friends == 2) {
	echo '<div id="friends">';
		if(isset($model->friends)) {
			echo ucwords($model->username . '\'s friends');
			foreach($model->friends as $friend) {
				?>
					<div id="friend">
					<div id="avatar">
					<?php
					$model->renderAvatar($friend, true);
				?>
					<div id='username'>
					<?php 
					echo CHtml::link(ucwords($friend->username), Yii::app()->createUrl('user/profile/view',array('id'=>$friend->id)));
				?>
					</div>
					</div>
					</div>
					<?php
			}
		} else {
			echo Yum::t('{username} has no friends yet', array('{username}' => $model->username)); 
		}
echo '</div>';
}
echo YumFriendshipController::invitationLink(Yii::app()->user->id, $model->id);
?>
