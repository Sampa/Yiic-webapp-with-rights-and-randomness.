<?php 
$this->pageTitle = Yii::app()->name . ' - '.Yum::t( "Profile");
$this->breadcrumbs=array(
		Yum::t('Profile') => array('profile'),
		Yum::t('Edit'));
$this->title = Yum::t('Edit profile');
?>

<h2> <?php echo Yum::t('Update my profile'); ?> </h2>
<div class="form">

<?php echo CHtml::beginForm(); ?>

<?php echo Yum::requiredFieldNote(); ?>

<?php echo CHtml::errorSummary(array($user, $profile)); ?>

<div class="row">
<?php echo CHtml::activeLabelEx($user,'username'); ?>
<?php echo CHtml::activeTextField($user,'username',array(
			'size'=>20,'maxlength'=>20)); ?>
<?php echo CHtml::error($user,'username'); ?>
</div>

<?php if(isset($profile) && is_object($profile)) 
	$this->renderPartial('/profile/_form', array('profile' => $profile)); ?>

	<div class="row buttons">
		<?php echo CHtml::submitButton($user->isNewRecord 
			? Yum::t('Create my profile') 
			: Yum::t('Save profile changes')); ?>
	</div>

	<?php echo CHtml::endForm(); ?>
<?php echo CHtml::button(Yum::t('Upload avatar Image'), array(
'submit' => array('/user/avatar/editAvatar'))); ?>

	</div><!-- form -->
