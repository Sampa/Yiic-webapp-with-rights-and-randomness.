<div class="form">

<?php echo CHtml::beginForm(); ?>

	<p class="note"><?php echo Yii::t("UserModule.user", 'Fields with <span class="required">*</span> are required.'); ?></p>

	<?php echo CHtml::errorSummary($model);
		  echo CHtml::errorSummary($profile); ?>

	<div class="row">
		<?php echo CHtml::activeLabelEx($model,'username'); ?>
		<?php echo CHtml::activeTextField($model,'username',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo CHtml::error($model,'username'); ?>
	</div>

	<div class="row">
		<?php echo CHtml::activeLabelEx($model,'password'); ?>
		<?php echo CHtml::activePasswordField($model,'password',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo CHtml::error($model,'password'); ?>
	</div>

	<div class="row">
		<?php echo CHtml::activeLabelEx($model,'superuser'); ?>
		<?php echo CHtml::activeDropDownList($model,'superuser',YumUser::itemAlias('AdminStatus')); ?>
		<?php echo CHtml::error($model,'superuser'); ?>
	</div>

	<div class="row">
		<?php echo CHtml::activeLabelEx($model,'status'); ?>
		<?php echo CHtml::activeDropDownList($model,'status',YumUser::itemAlias('UserStatus')); ?>
		<?php echo CHtml::error($model,'status'); ?>
	</div>
<?php 
		$profileFields=YumProfileField::model()->forOwner()->sort()->findAll();
if ($profileFields) 
{
	foreach($profileFields as $field) 
	{
			?>
	<div class="row">
		<?php echo CHtml::activeLabelEx($profile,$field->varname); ?>
		<?php 
		if ($field->field_type=="TEXT") {
			echo CHtml::activeTextArea($profile,$field->varname,array('rows'=>6, 'cols'=>50));
		} else {
			echo CHtml::activeTextField($profile,$field->varname,array('size'=>60,'maxlength'=>(($field->field_size)?$field->field_size:255)));
		}
		 ?>
		<?php echo CHtml::error($profile,$field->varname); ?>
	</div>	
			<?php
			}
		}
?>


<?php if($this->module->hasModule('role')): ?>
<div class="row">
<p> <?php echo Yii::t('UserModule.user', 'User belongs to these Roles'); ?>: </p>

<?php 
		$this->widget('YumModule.components.Relation',
			array('model' => $model,
			'relation' => 'roles',
			'style' => 'dropdownlist',
			'fields' => 'title',
			'showAddButton' => false
		));  ?>

</div>

<div class="row">
<p> <?php echo Yii::t('UserModule.user', 'This user can administrate this users'); ?>: </p>

<?php 
		$this->widget('YumModule.components.Relation',
			array('model' => $model,
			'relation' => 'users',
			'style' => 'listbox',
			'fields' => 'username',
			'showAddButton' => false
		));  ?>

</div>


<?php endif; ?>


<div class="row buttons">
<?php echo CHtml::submitButton($model->isNewRecord 
		? Yii::t('UserModule.user', 'Create') 
		: Yii::t('UserModule.user', 'Save')); ?>
		</div>

		<?php echo CHtml::endForm(); ?>

		</div><!-- form -->
