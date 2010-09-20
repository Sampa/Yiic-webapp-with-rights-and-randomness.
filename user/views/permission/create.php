<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'permission-create-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
	<?php echo $form->labelEx($model,'principal_id'); ?>
	<?php $this->widget('Relation', array(
				'model' => $model,
				'relation' => 'principal',
				'fields' => 'username',
				));?>
		<?php echo $form->error($model,'principal_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'subordinate_id'); ?>
		<?php $this->widget('Relation', array(
					'model' => $model,
					'relation' => 'subordinate',
					'fields' => 'username',
					));?>

		<?php echo $form->error($model,'subordinate_id'); ?>
		</div>

	<div class="row">
		<?php echo $form->labelEx($model,'type'); ?>
		<?php echo $form->textField($model,'type'); ?>
		<?php echo $form->error($model,'type'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'action'); ?>
		<?php $this->widget('Relation', array(
					'model' => $model,
					'relation' => 'action',
					'fields' => 'title',
					));?>
		<?php echo $form->error($model,'action'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'comment'); ?>
		<?php echo $form->textArea($model,'comment'); ?>
		<?php echo $form->error($model,'comment'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'template'); ?>
		<?php echo $form->checkBox($model,'template'); ?>
		<?php echo $form->error($model,'template'); ?>
	</div>


	<div class="row buttons">
		<?php echo CHtml::submitButton('Submit'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
