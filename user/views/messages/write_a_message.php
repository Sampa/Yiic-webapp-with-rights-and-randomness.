<?php
if(Yum::module()->messageSystem != YumMessage::MSG_NONE && $model->id != Yii::app()->user->id) {

	$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
				'id'=>'message',
				'options'=>array(
					'model' => true,
					'title'=>$model->username,
					'autoOpen'=>false,
					),
				));

	$this->renderPartial('/messages/compose', array(
				'model' => new YumMessage,
				'to_user_id' => $model->id), false, true);

	$this->endWidget('zii.widgets.jui.CJuiDialog');

	echo CHtml::link(Yum::t('Write a message to this User'), '#',
			array('onclick'=>'$("#message").dialog("open"); return false;'));
}
?>
