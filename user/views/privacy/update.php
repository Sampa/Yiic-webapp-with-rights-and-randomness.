<?php
$this->breadcrumbs=array(
		Yum::t('Privacysettings')=>array('index'),
		$model->user->username=>array('//user/user/view','id'=>$model->user_id),
		Yum::t( 'Update'),
		);

echo Yum::t('Privacy settings for {username}', array('{username}' => $model->user->username));

?>
<div class="form">
<p class="note">
<?php Yum::requiredFieldNote(); ?>
</p>

<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'privacysetting-form',
			'enableAjaxValidation'=>true,
			)); 
echo $form->errorSummary($model);
?>
<div class="row">
<?php echo $form->labelEx($model,'message_new_friendship'); ?>
<?php echo $form->dropDownList($model, 'message_new_friendship', array(
			0 => Yum::t('No'),
			1 => Yum::t('Yes'))); ?>
<?php echo $form->error($model,'message_new_friendship'); ?>
</div>

<div class="row">
<?php echo $form->labelEx($model,'message_new_message'); ?>
<?php echo $form->dropDownList($model, 'message_new_message', array(
			0 => Yum::t('No'),
			1 => Yum::t('Yes'))); ?>

<?php echo $form->error($model,'message_new_message'); ?>
</div>

<div class="row">
<?php echo $form->labelEx($model,'message_new_profilecomment'); ?>
<?php echo $form->dropDownList($model, 'message_new_profilecomment', array(
			0 => Yum::t('No'),
			1 => Yum::t('Yes'))); ?>
<?php echo $form->error($model,'message_new_profilecomment'); ?>
</div>

<?php
echo CHtml::Button(Yum::t( 'Cancel'), array(
			'submit' => array('//user/profile/view')));
echo CHtml::submitButton(Yum::t('Save')); 
$this->endWidget(); ?>
</div> <!-- form -->
