<?php
if(Yum::module()->messageSystem != YumMessage::MSG_NONE && $model->id != Yii::app()->user->id) {
	echo CHtml::link(Yum::t('Write a message to this User'), array(
				'messages/compose', 'to_user_id' => $model->id));
}
	echo '<br />';
?>
