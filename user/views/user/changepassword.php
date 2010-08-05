<?php 
$this->pageTitle=Yii::app()->name . ' - '.Yii::t('UserModule.user', "Change password");
$this->title = Yii::t('UserModule.user', "Change password");
$this->breadcrumbs = array(
	Yii::t('UserModule.user', "Profile") => array('profile'),
	Yii::t('UserModule.user', "Change password"));
?>

<div class="form">
<?php echo CHtml::beginForm(); ?>

	<?php echo Yum::requiredFieldNote(); ?>
	<?php echo CHtml::errorSummary($form); ?>
	<?php $this->renderPartial('passwordfields', array( 'form'=>$form)); ?>

	
	<div class="row submit">
	<?php echo CHtml::submitButton(Yii::t('UserModule.user', "Save")); ?>
	</div>

<?php echo CHtml::endForm(); ?>
</div><!-- form -->
