<?php 
$this->title = Yum::t('Composing new message');
$this->breadcrumbs = array(
		Yum::t('Messages') => array('index'),
		Yum::t('Compose new message'),
		);
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'yum-messages-form',
			'enableAjaxValidation'=>true,
			)); ?>

<?php echo Yum::requiredFieldNote(); 

echo $form->errorSummary($model); 

if(isset($to_user_id) && $to_user_id !== false) {
	echo CHtml::hiddenField('YumMessage[to_user_id]', $to_user_id);
	echo Yum::t('This message will be sent to {username}', array(
				'{username}' => YumUser::model()->findByPk($to_user_id)->username));
} else {
	echo '<div class="row">';
	printf('<p>%s</p>',
			Yum::t('Select multiple recipients by holding the CTRL key')); 

	echo CHtml::ListBox('YumMessage[to_user_id]',
			isset($_GET['to_user_id']) ? $_GET['to_user_id'] :'', CHtml::listData( 
				YumUser::model()->active()->findAll(), 'id', 'username'),
			array('multiple' => 'multiple', 'style' => 'width:300px; height:200px;'));

	echo $form->error($model,'to_user_id'); 
	echo '</div>';
}

?>
<div class="row">
<?php echo $form->labelEx($model,'title'); ?>
<?php echo $form->textField($model,'title',array('size'=>45,'maxlength'=>45)); ?>
<?php echo $form->error($model,'title'); ?>
</div>

<div class="row">
<?php echo $form->labelEx($model,'message'); ?>
<?php echo $form->textArea($model,'message',array('rows'=>6, 'cols'=>50)); ?>
<?php echo $form->error($model,'message'); ?>
</div>

<div class="row buttons">
<?php echo CHtml::submitButton($model->isNewRecord ? Yii::t("UserModule.user", 'Send') : Yii::t('UserModule.user', 'Save')); ?>
</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
