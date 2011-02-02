<?php 
$this->pageTitle = Yii::app()->name . ' - '.Yum::t( 'Registration');
$this->title = Yum::t('Registration');
$this->breadcrumbs = array(Yum::t('Registration'));
Yum::renderFlash(); ?>

<div class="form">
<?php echo CHtml::beginForm(); ?>
<?php echo Yum::requiredFieldNote(); ?>
<?php echo CHtml::errorSummary($form, $profile); ?>

<div class="row">
<?php
echo CHtml::activeLabelEx($form,'username');
echo CHtml::activeTextField($form,'username');
echo CHtml::activeLabelEx($profile,'email');
echo CHtml::activeTextField($form,'username');
?>
</div>

<?php 
$profileFields = YumProfileField::model()->forRegistration()->sort()->findAll();

if ($profileFields) {
	if(!isset($profile))
		$profile = new YumProfile();

	foreach($profileFields as $field)
	{
?>
			<div class="row">
<?php
		if ($field->range) 
		{
				echo CHtml::activeLabelEx($profile, $field->varname);
				echo CHtml::activeDropDownList($profile,
					$field->varname,
					YumProfile::range($field->range));
		}
		elseif ($field->varname == 'email')
		{
			//Paint it hidden or paint it like a text field, depending of the method of registration.
			if(Yum::module()->loginType & UserModule::LOGIN_BY_EMAIL)
				echo CHtml::activeHiddenField ($profile, 'email');
			else
			{
				echo CHtml::activeLabelEx($profile, $field->varname);
				echo CHtml::activeTextArea($profile, $field->varname, array('rows'=>6, 'cols'=>50));
			}

			$tmp_profile=YumProfile::model()->find('email=\''.$form->email.'\'');
			if ($tmp_profile !== null)
			{
				$user=$tmp_profile->user;
				if ($user !== null && $user->status == YumUser::STATUS_NOTACTIVE)
					echo $this->renderPartial('/user/_resend_activation_partial', array('user'=>$user, 'user'=>$user));
			}
		}
		elseif ($field->field_type == "TEXT")
		{
			echo CHtml::activeLabelEx($profile, $field->varname);
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
			echo CHtml::activeLabelEx($profile, $field->varname);
			echo CHtml::activeTextField($profile,
					$field->varname,
					array(
						'size'=>60,
						'maxlength'=>(($field->field_size)?$field->field_size:255)));
		}
		?>
			</div>  
			<?php
	}
}
?>
	<div class="row">
	<?php echo CHtml::activeLabelEx($form,'password'); ?>
	<?php echo CHtml::activePasswordField($form,'password'); ?>
	</div>
	
	<div class="row">
	<?php echo CHtml::activeLabelEx($form,'verifyPassword'); ?>
	<?php echo CHtml::activePasswordField($form,'verifyPassword'); ?>
	</div>

	<?php if(extension_loaded('gd') && Yum::module()->enableCaptcha): ?>
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

<?php echo CHtml::endForm(); ?>
</div><!-- form -->
