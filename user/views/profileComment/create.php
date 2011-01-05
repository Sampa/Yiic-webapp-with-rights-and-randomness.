<div class="form">
<p class="note"> <?php echo Yum::requiredFieldNote(); ?> </p>

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
echo CHtml::Button(Yum::t('Write comment'), array(
	'id' => 'write_comment',
	));

if(!Yii::app()->clientScript->isScriptRegistered("write_comment"))
Yii::app()->clientScript->registerScript("write_comment", " 
$('#write_comment').unbind('click');
$('#write_comment').click(function(){
jQuery.ajax({'type':'POST',
'url':'".$this->createUrl('//user/comments/create')."',
'cache':false,
'data':jQuery(this).parents('form').serialize(),
'success':function(html){
$('#profile').html(html);
}});
return false;});
");


$this->endWidget(); ?>

</div>
