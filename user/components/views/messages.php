<?php
if($messages) {
	echo '<table>';
	printf('<th>%s</th><th>%s</th><th>%s</th><th>%s</th>',
		Yum::t('Status'),
		Yum::t('From'),
		Yum::t('Subject'),
		Yum::t('Actions'));
	foreach($messages as $message) {
		printf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s %s %s</td></tr>',
				$message->getStatus(),
				$message->from_user->username,
				CHtml::link($message->title, array('//user/messages/view', 'id' => $message->id)),
				$message->from_user_id != Yii::app()->user->id 
				? CHtml::link(Yum::t('Reply'), array('//user/messages/compose', 'to_user_id' => $message->from_user_id))
				: '',
				!$message->message_read
				? CHtml::link(Yum::t('Mark as read'), array('//user/messages/mark_as_read', 'id' => $message->id))
				: '',
				$message->from_user_id != Yii::app()->user->id 
				? CHtml::link(Yum::t('Remove'), array(
						'//user/messages/delete', 'id' => $message->id), array('confirm' => Yum::t('Are you sure?')))
				: '');
	}
	echo '</table>';
} else
echo Yum::t('You have no messages yet'); 
?>
