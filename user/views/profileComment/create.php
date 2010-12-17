<div class="form">
<p class="note">
<?php echo Yum::t('Fields with');?> <span class="required">*</span> <?php echo Yum::t('are required');?>.
</p>

<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'profile-comment-form',
			'enableAjaxValidation'=>true,
			)); 
echo $form->errorSummary($model);
?>

<?php echo CHtml::hiddenField('YumProfileComment[profile_id]', $profile->profile_id); ?>

<div class="row">
<?php echo $form->labelEx($model,'comment'); ?>
<?php echo $form->textArea($model,'comment',array('rows'=>6, 'cols'=>50)); ?>
<?php echo $form->error($model,'comment'); ?>
</div>

<?php
echo CHtml::Button(Yum::t('Cancel'),  array(
   'onclick'=>'$("#profileComment").dialog("close"); return false;',
));

echo CHtml::ajaxSubmitButton(Yum::t('Write comment'), array(
			'//user/comments/create'), array(
					'update' => "#profileComment"));

$this->endWidget(); ?>

</div>
