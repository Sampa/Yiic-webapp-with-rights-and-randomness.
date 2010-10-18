<?php 
$this->pageTitle = Yii::app()->name . ' - ' . Yum::t("change password");
$this->title = Yum::t('change password');

$this->breadcrumbs = array(
	Yum::t("Profile") => array('profile'),
	Yum::t("Change password"));

if($expired)
	$this->renderPartial('password_expired');
?>

<div class="form">
<?php echo CHtml::beginForm(); ?>

	<?php echo Yum::requiredFieldNote(); ?>
	<?php echo CHtml::errorSummary($form); ?>
	<?php $this->renderPartial('/user/passwordfields', array('form'=>$form)); ?>

	
	<div class="row submit">
	<?php echo CHtml::submitButton(Yum::t("Save")); ?>
	</div>

<?php echo CHtml::endForm(); ?>
</div><!-- form -->
