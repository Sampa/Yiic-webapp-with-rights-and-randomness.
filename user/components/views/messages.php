<?php
if($messages) {
	echo '<table class="new_messages">';
	printf('<th>%s</th><th>%s</th>',
			Yum::t('From'),
			Yum::t('Subject'));
	foreach($messages as $message) {
		printf('<tr><td>%s</td><td>%s</td></tr>',
				$message->from_user->username,
				CHtml::link($message->title, array('//user/messages/view', 'id' => $message->id)));
	}
	echo '</table>';
} else
echo Yum::t('No new messages'); 
?>
