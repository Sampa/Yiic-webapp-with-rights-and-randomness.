<?php 
$this->title = Yii::t('UserModule.user','Composing new message');
$this->breadcrumbs = array(
	Yii::t('UserModule.user', 'Messages') => array('index'),
	Yii::t('UserModule.user', 'Compose new message'),
);
?>

<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'yum-messages-form',
	'enableAjaxValidation'=>true,
)); ?>

	<?php echo Yum::requiredFieldNote(); ?>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<p> <?php echo Yii::t('UserModule.user', 
		'Select multiple recipients by holding the CTRL key'); ?> </p>

<?php 
echo CHtml::ListBox('YumMessage[to_user_id]',
		isset($_GET['to_user_id']) ? $_GET['to_user_id'] :'', CHtml::listData( 
			YumUser::model()->active()->findAll(), 'id', 'username'),
		array('multiple' => 'multiple', 'style' => 'width:300px; height:200px;'));
?>
<?php echo $form->error($model,'to_user_id'); ?>
	</div>

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
