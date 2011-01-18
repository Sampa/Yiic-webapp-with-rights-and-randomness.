<?php
if($comments) {
	echo Yum::t('This users have commented your profile recently') . '<br />';
	foreach($comments as $comment)
		if(isset($comment->user) && $comment->user_id != Yii::app()->user->id)
			printf('<div style="float:left">%s %s</div>', 
					CHtml::link($comment->user->getAvatar(true), array(
							'//user/profile/view', 'id' => $comment->user_id)),
					CHtml::link($comment->user->username, array(
							'//user/profile/view', 'id' => $comment->user_id)));
} else
echo Yum::t('Nobody has commented your profile yet');
?>

<div style="clear: both;"> </div>
