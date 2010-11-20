<?php 
$this->pageTitle = Yii::app()->name . ' - '.Yii::t("user", "Registration");

$this->title = Yii::t("UserModule.user", "Registration");

$this->breadcrumbs = array(Yii::t("UserModule.user", "Registration"));
?>

<?php if(Yii::app()->user->hasFlash('registration')): ?>
<div class="success">
<?php echo Yii::app()->user->getFlash('registration'); ?>
</div>
<?php else: ?>

<?php
$settings = YumTextSettings::model()->find("language = :language", array(
			':language' => Yii::app()->language));

if($settings) 
	printf('%s<br /><br />', $settings->text_registration_header);

?>


<div class="form">
<?php echo CHtml::beginForm(); ?>

	<?php echo Yum::requiredFieldNote(); ?>
	<?php echo CHtml::errorSummary($form); ?>

<?php
if(Yii::app()->getModule('user')->enableRoles) {
	$roles = YumRole::model()->selectable()->findAll();
	if(count($roles) > 0) {
		printf('<p>%s:</p>', Yum::t('Designation'));
		// render a Radio Button list and preselect the first entry:
		$data = CHtml::listData($roles, 'id', 'title');
		echo CHtml::radioButtonList('roles', key($data),
				$data,
				array('labelOptions' => array('style' => 'display: inline;')));

	}
}
?>


	

<?php
	$loginType = Yii::app()->getModule('user')->loginType;

	if($loginType != 'LOGIN_BY_EMAIL' ) {
		echo CHtml::activeLabelEx($form,'username'); 
		echo CHtml::activeTextField($form,'username'); 
		echo CHtml::error($form,'username'); 
	}else{
		echo CHtml::activeHiddenField($form,'username');
	}
	
?>

	<?php 
	$profileFields = YumProfileField::model()->forRegistration()->sort()->findAll();

if ($profileFields) {
	if(!isset($profile))
		$profile = new YumProfile();

	foreach($profileFields as $field) {
		?>
			<div class="row">
			<?php echo CHtml::activeLabelEx($profile, $field->varname); ?>
			<?php 
			if ($field->range) 
			{
				echo CHtml::activeDropDownList($profile,
						$field->varname,
						YumProfile::range($field->range));
			}
		elseif ($field->field_type == "TEXT") 
		{
			echo CHtml::activeTextArea($profile,
					$field->varname, 
					array('rows'=>6, 'cols'=>50));
		}
		elseif ($field->field_type == "DROPDOWNLIST") 
		{
			if($field->required == 2)
				$req = array('empty' => '--');
			else
				$req = array();
			echo CHtml::activeDropDownList($profile,
					$field->varname, 
					CHtml::listData(CActiveRecord::model(ucfirst($field->varname))->findAll(),
						'id',
						$field->related_field_name), $req);

		}

			else 
			{
				echo CHtml::activeTextField($profile,
						$field->varname,
						array(
							'size'=>60,
							'maxlength'=>(($field->field_size)?$field->field_size:255)));
			}
		?>
			<?php echo CHtml::error($profile,$field->varname); ?>
			</div>  
			<?php
	}
}
?>
<?php 

if($registrationtype != YumRegistration::REG_NO_PASSWORD &&  $registrationtype != YumRegistration::REG_NO_PASSWORD_ADMIN_CONFIRMATION)
{
	?>
	<div class="row">
	<?php echo CHtml::activeLabelEx($form,'password'); ?>
	<?php echo CHtml::activePasswordField($form,'password'); ?>
	</div>
	
	<div class="row">
	<?php echo CHtml::activeLabelEx($form,'verifyPassword'); ?>
	<?php echo CHtml::activePasswordField($form,'verifyPassword'); ?>
	</div>
<?php 
}
?>
	<?php if(extension_loaded('gd') && Yii::app()->getModule('user')->enableCaptcha): ?>
	<div class="row">
		<?php echo CHtml::activeLabelEx($form,'verifyCode'); ?>
		<div>
		<?php $this->widget('CCaptcha'); ?>
		<?php echo CHtml::activeTextField($form,'verifyCode'); ?>
		</div>
		<p class="hint"><?php echo Yii::t("UserModule.user","Please enter the letters as they are shown in the image above."); ?>
		<br/><?php echo Yii::t("UserModule.user","Letters are not case-sensitive."); ?></p>
	</div>
	<?php endif; ?>
	
<?php
if($settings) 
	printf('%s<br /><br />', $settings->text_registration_footer);
?>

	<div class="row submit">
		<?php echo CHtml::submitButton(Yii::t("UserModule.user", "Registration")); ?>
	</div>

<?php echo CHtml::endForm(); ?>
</div><!-- form -->
<?php endif; ?>
