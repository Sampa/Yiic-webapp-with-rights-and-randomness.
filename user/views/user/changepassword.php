<?php $this->pageTitle=Yii::app()->name . ' - '.Yii::t('UserModule.user', "Change password");
$this->breadcrumbs=array(
	Yii::t('UserModule.user', "Profile") => array('profile'),
	Yii::t('UserModule.user', "Change password"),
	);
?>

<?php $this->menu = array( YumMenuItemHelper::backToProfile() );?>

<h1><?php echo Yii::t('UserModule.user', "Change password"); ?></h1>

<div class="form">
<?php echo CHtml::beginForm(); ?>

	<p class="note"><?php echo Yii::t('UserModule.user', 'Fields with <span class="required">*</span> are required.'); ?></p>
	<?php echo CHtml::errorSummary($form); ?>
	
	<div class="row">
	<?php echo CHtml::activeLabelEx($form,'password'); ?>
	<?php echo CHtml::activePasswordField($form,'password'); ?>
	<p class="hint">
	<?php echo Yii::t('UserModule.user', "Minimal password length 4 symbols."); ?>
	</p>
	</div>
	
	<div class="row">
	<?php echo CHtml::activeLabelEx($form,'verifyPassword'); ?>
	<?php echo CHtml::activePasswordField($form,'verifyPassword'); ?>
	</div>
	
	
	<div class="row submit">
	<?php echo CHtml::submitButton(Yii::t('UserModule.user', "Save")); ?>
	</div>

<?php echo CHtml::endForm(); ?>
</div><!-- form -->
