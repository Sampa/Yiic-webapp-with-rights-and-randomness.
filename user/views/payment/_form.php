<div class="form">
<p class="note">
<?php echo Yii::t('app','Fields with');?> <span class="required">*</span> <?php echo Yii::t('app','are required');?>.
</p>

<?php $form=$this->beginWidget('CActiveForm', array(
'id'=>'payment-form',
	'enableAjaxValidation'=>true,
	)); 
	echo $form->errorSummary($model);
?>
	<div class="row">
<?php echo $form->labelEx($model,'title'); ?>
<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
<?php echo $form->error($model,'title'); ?>
<?php if('_HINT_Payment.title' != $hint = Yii::t('app', '_HINT_Payment.title')) echo $hint; ?>
</div>

<div class="row">
<?php echo $form->labelEx($model,'text'); ?>
<?php echo $form->textArea($model,'text',array('rows'=>6, 'cols'=>50)); ?>
<?php echo $form->error($model,'text'); ?>
<?php if('_HINT_Payment.text' != $hint = Yii::t('app', '_HINT_Payment.text')) echo $hint; ?>
</div>


<?php
echo CHtml::Button(Yii::t('app', 'Cancel'), array(
			'submit' => array('payment/admin'))); 
echo CHtml::submitButton(Yii::t('app', 'Save')); 
$this->endWidget(); ?>
</div> <!-- form -->
