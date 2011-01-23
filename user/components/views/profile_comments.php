<?php
if($commentators) {
	echo Yum::t('This users have commented your profile recently') . '<br />';
	foreach($commentators as $commentator)
			printf('<div style="float:left">%s %s</div>', 
					CHtml::link($commentator->getAvatar(true), array(
							'//user/profile/view', 'id' => $commentator->id)),
					CHtml::link($commentator->username, array(
							'//user/profile/view', 'id' => $commentator->id)));
} else
echo Yum::t('Nobody has commented your profile yet');
?>

<div style="clear: both;"> </div>
