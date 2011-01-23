<?php
$this->breadcrumbs=array(
		Yum::t('Privacysettings')=>array('index'),
		$model->user->username=>array(
			'//user/user/view','id'=>$model->user_id),
		Yum::t( 'Update'),
		);

$this->title = Yum::t('Privacy settings for {username}', array(
			'{username}' => $model->user->username));

?>
<div class="form">
<p class="note">
<?php Yum::requiredFieldNote(); ?>
</p>

<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'privacysetting-form',
			'enableAjaxValidation'=>true,
			)); 
echo $form->errorSummary($model);
?>

<?php if(Yum::module()->enableProfiles) { ?>
<div class="row profile_field_selection">
<?php
echo '<h2>' . Yum::t('Profile field public options') . '</h2>';
echo '<p>' . Yum::t('Select the fields that should be public') . ':</p>';
$i = 1;
foreach(YumProfileField::model()->findAll() as $field) {
	printf('<label class="profilefieldlabel" 
			for="privacy_for_field_%d">%s</label>%s<br />',
			$i,
			$field->title,
			CHtml::checkBox("privacy_for_field_{$i}",
				$model->public_profile_fields & $i)
			);

	$i *= 2;
}
?>
</div>

<?php } ?>

<div class="row">
<?php echo $form->labelEx($model,'message_new_friendship'); ?>
<?php echo $form->dropDownList($model, 'message_new_friendship', array(
			0 => Yum::t('No'),
			1 => Yum::t('Yes'))); ?>
<?php echo $form->error($model,'message_new_friendship'); ?>
</div>

<div class="row">
<?php echo $form->labelEx($model,'message_new_message'); ?>
<?php echo $form->dropDownList($model, 'message_new_message', array(
			0 => Yum::t('No'),
			1 => Yum::t('Yes'))); ?>

<?php echo $form->error($model,'message_new_message'); ?>
</div>

		<div class="row">
	<?php 
	echo CHtml::activeLabelEx($profile, 'allow_comments'); 
	echo CHtml::activeDropDownList($profile, 'allow_comments',
			array(
				'0' => Yum::t( 'No'),
				'1' => Yum::t( 'Yes'),
				)
			);
	?>
	</div>


	<?php if(Yum::module()->enableProfiles) { ?>

		<?php if(Yum::module()->enableProfileComments) { ?>
			<div class="row">
				<?php echo $form->labelEx($model,'message_new_profilecomment'); ?>
				<?php echo $form->dropDownList($model, 'message_new_profilecomment', array(
							0 => Yum::t('No'),
							1 => Yum::t('Yes'))); ?>
				<?php echo $form->error($model,'message_new_profilecomment'); ?>
				</div>

				<div class="row">
				<?php 
				echo CHtml::activeLabelEx($profile, 'allow_comments'); 
			echo CHtml::activeDropDownList($profile, 'allow_comments',
					array(
						'0' => Yum::t( 'No'),
						'1' => Yum::t( 'Yes'),
						)
					);
			?>
				</div>
				<?php } ?>

				<?php } ?>

<?php if(Yum::module()->enableFriendship) { ?>
	<div class="row">
	<?php 
	echo CHtml::activeLabelEx($profile, 'show_friends'); 
	echo CHtml::activeDropDownList($profile, 'show_friends',
			array(
				'0' => Yum::t( 'do not make my friends public'),
				'1' => Yum::t( 'only to my friends'),
				'2' => Yum::t( 'make my friends public'),
				)
			);
	?>
	</div>
<?php } ?>

<?php if(Yum::module()->enableOnlineStatus) { ?>
	<div class="row">
	<?php 
	echo CHtml::activeLabelEx($model, 'show_online_status'); 
	echo CHtml::activeDropDownList($model, 'show_online_status',
			array(
				'0' => Yum::t( 'Do not show my online status'),
				'1' => Yum::t( 'Show my online status to everyone'),
				)
			);
	?>
	</div>
<?php } ?>

<?php if(Yum::module()->enableProfiles) { ?>
	<div class="row">
	<?php 
	echo CHtml::activeLabelEx($model, 'log_profile_visits'); 
	echo CHtml::activeDropDownList($model, 'log_profile_visits',
			array(
				'0' => Yum::t( 'Do not show the owner of a profile when i visit him'),
				'1' => Yum::t( 'Show the owner when i visit his profile'),
				)
			);
	?>
	</div>
<?php } ?>

<?php if(Yum::module()->enableRoles) { ?>
	<div class="row">
	<?php 
	echo CHtml::activeLabelEx($model, 'appear_in_search'); 
	echo CHtml::activeDropDownList($model, 'appear_in_search',
			array(
				'0' => Yum::t( 'Do not appear in search'),
				'1' => Yum::t( 'Appear in search'),
				)
			);
	?>
	</div>
<?php } ?>

<div class="row">
<?php echo $form->labelEx($model,'ignore_users'); ?>
<?php echo $form->textField($model, 'ignore_users',  array('size' => 100)); ?>
<?php echo $form->error($model,'ignore_users'); ?>
<div class="hint">
<p> <?php echo Yum::t('Separate usernames with comma to ignore specified users'); ?> </p>
</div>
</div>


<?php
echo CHtml::Button(Yum::t( 'Cancel'), array(
			'submit' => array('//user/profile/view')));
echo CHtml::submitButton(Yum::t('Save')); 
$this->endWidget(); ?>
</div> <!-- form -->
