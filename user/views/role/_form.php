<div class="form">

<?php echo CHtml::beginForm(); ?>

<?php echo Yum::requiredFieldNote(); ?>

<?php echo CHtml::errorSummary($model); ?>

<div class="row">
<?php echo CHtml::activeLabelEx($model,'title'); ?>
<?php echo CHtml::activeTextField($model,'title',array('size'=>20,'maxlength'=>20)); ?>
<?php echo CHtml::error($model,'title'); ?>
</div>

<div class="row">
<?php echo CHtml::activeLabelEx($model,'description'); ?>
<?php echo CHtml::activeTextArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
<?php echo CHtml::error($model,'description'); ?>
</div>	

<div class="row">
<p> <?php echo Yii::t('UserModule.user', 'This users have been assigned to this role'); ?> </p>

<?php 
$this->widget('YumModule.components.Relation',
		array('model' => $model,
			'relation' => 'users',
			'style' => 'checkbox',
			'fields' => 'username',
			'htmlOptions' => array(
				'checkAll' => Yum::t('Choose All'),
				'template' => '<div style="float:left;margin-right:5px;">{input}</div>{label}'),
			'showAddButton' => false
			));  

printf('<p>%s:</p>', Yum::t('This role can administer users of this roles'));
$this->widget('YumModule.components.Relation',
		array('model' => $model,
			'relation' => 'roles',
			'style' => 'checkbox',
			'fields' => 'title',
			'htmlOptions' => array(
				'checkAll' => Yum::t('Choose All'),
				'template' => '<div style="float:left;margin-right:5px;">{input}</div>{label}'),
			'showAddButton' => false
			));
echo CHtml::closeTag('div');
?>

<div class="row">
<?php echo CHtml::activeLabelEx($model,'selectable'); ?>
<?php echo CHtml::activeCheckBox($model, 'selectable'); ?>
</div>

<div class="row buttons">
<?php echo CHtml::submitButton($model->isNewRecord 
		? Yii::t('UserModule.user', 'Create') 
		: Yii::t('UserModule.user', 'Save')); ?>
</div>

<?php echo CHtml::endForm(); ?>

</div><!-- form -->
