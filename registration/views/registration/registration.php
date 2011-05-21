<h2> Yii User Management example registration Page </h2>
<p> This is an example how your registration page could look. </p>
<p> To override this example registration page with your
project-specific, read modules/user/docs/registration.txt </p>

<?php $this->breadcrumbs = array(Yum::t('Registration')); ?>

<div class="form">
<?php $activeform = $this->beginWidget('CActiveForm', array(
			'id'=>'registration-form',
			'enableAjaxValidation'=>true,
			'focus'=>array($form,'username'),
			));
?>

<?php echo Yum::requiredFieldNote(); ?>
<?php echo CHtml::errorSummary(array($form, $profile)); ?>

<div class="row"> <?php
echo $activeform->labelEx($form,'username');
echo $activeform->textField($form,'username');
?> </div>

<div class="row"> <?php
echo $activeform->labelEx($profile,'firstname');
echo $activeform->textField($profile,'firstname');
?> </div>  

<div class="row"> <?php
echo $activeform->labelEx($profile,'lastname');
echo $activeform->textField($profile,'lastname');
?> </div>  

<div class="row">
<?php echo $activeform->labelEx($form,'password'); ?>
<?php echo $activeform->passwordField($form,'password'); ?>
</div>

<div class="row">
<?php echo $activeform->labelEx($form,'verifyPassword'); ?>
<?php echo $activeform->passwordField($form,'verifyPassword'); ?>
</div>

<?php if(extension_loaded('gd') 
			&& Yum::module('registration')->enableCaptcha): ?>
	<div class="row">
		<?php echo CHtml::activeLabelEx($form,'verifyCode'); ?>
		<div>
		<?php $this->widget('CCaptcha'); ?>
		<?php echo CHtml::activeTextField($form,'verifyCode'); ?>
		</div>
		<p class="hint"><?php echo Yum::t('Please enter the letters as they are shown in the image above.'); ?>
		<br/><?php echo Yum::t('Letters are not case-sensitive.'); ?></p>
	</div>
	<?php endif; ?>
	
	<div class="row submit">
		<?php echo CHtml::submitButton(Yum::t('Registration')); ?>
	</div>

<?php $this->endWidget(); ?>
</div><!-- form -->
