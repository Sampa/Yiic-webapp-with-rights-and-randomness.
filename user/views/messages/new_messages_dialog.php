<?php
	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
				'id'=>rand(1, 999999),
				'options'=>array(
					'show' => 'blind',
					'hide' => 'explode',
					'modal' => 'true',
					'title' => Yum::t('You have {count} new Messages !', array(
							'{count}' => count($messages))),
						'autoOpen'=>true,
					),
				));

echo '<table>';
foreach($messages as $message) {
	printf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
			$message->from_user->username,
			CHtml::link($message->title, array('//user/messages/view', 'id' => $message->id)),
			CHtml::link(Yum::t('View'), array('//user/messages/view', 'id' => $message->id)),
			CHtml::link(Yum::t('Reply'), array('//user/messages/compose', 'to_user_id' => $message->from_user_id)));


}
	echo '</table>';
	$this->endWidget('zii.widgets.jui.CJuiDialog');
?>
