<?php
if($comments) {
	echo Yum::t('This users have commented your profile recently') . '<br />';
	foreach($comments as $comment) {
			printf('<div style="float:left;margin: 0px 10px;">%s %s</div>', 
					CHtml::link($comment->user->getAvatar(true), array(
							'//user/profile/view', 'id' => $comment->user_id)),
					CHtml::link($comment->user->username, array(
							'//user/profile/view', 'id' => $comment->user_id)));
			printf('<div style="float:left;width:200px;">%s </div>', 
						substr($comment->comment, 0, 100));
	echo '<div style="clear: both;"></div>';

}
} else
echo Yum::t('Nobody has commented your profile yet');
?>

<div style="clear: both;"> </div>
