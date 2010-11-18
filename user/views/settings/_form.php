<p class="note"><?php echo Yii::t('app','Fields with');?> <span class="required">*</span> <?php echo Yii::t('app','are required');?>.</p>

<?php echo $form->errorSummary($model); ?>

<div class="row">
<?php echo $form->labelEx($model,'title'); ?>
<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
<?php printf('<p class="tooltip">%s</p>', Yii::t('UserModule.user', 'Title of this settings profile')); ?>
<?php echo $form->error($model,'title'); ?>
</div>

<div class="row">
<?php echo $form->labelEx($model,'enableProfileHistory'); ?>
<?php echo $form->dropDownList($model,'enableProfileHistory', array(
0 => Yum::t('Disable profile History'),
1 => Yum::t('Enable profile History')
)); ?>
<?php printf('<p class="tooltip">%s</p>', Yii::t('UserModule.user', 'If enable History is set, user profiles are kept in the system even if the user changes his profile Data. This way the admin is able to see what has changed in the profile history log.')); ?>
<?php echo $form->error($model,'enableProfileHistory'); ?>
</div>

<div class="row">
<?php echo $form->labelEx($model,'readOnlyProfiles'); ?>

<?php echo $form->dropDownList($model,'readOnlyProfiles', array(
0 => Yum::t('Profiles can be changed by their users'),
1 => Yum::t('Profiles are read-only')
)); ?>

<?php printf('<p class="tooltip">%s</p>', Yii::t('UserModule.user', 'Should users be able to update their profile data?')); ?>
<?php echo $form->error($model,'readOnlyProfiles'); ?>
</div>

<div class="row">
<?php echo $form->labelEx($model,'preserveProfiles'); ?>
<?php echo $form->dropDownList($model,'preserveProfiles', array(
0 => Yum::t('Do not keep user profiles'),
1 => Yum::t('Keep all User profiles')
)); ?>
<?php printf('<p class="tooltip">%s</p>', Yii::t('UserModule.user', 'If preserveProfiles is set, the profiles are not removed when the user gets deleted. This way the administrator keeps his user profile history forever')); ?>
<?php echo $form->error($model,'preserveProfiles'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'registrationType'); ?>
<?php echo $form->dropDownList($model,'registrationType', array(
0 => Yum::t('Disable registration'),
1 => Yum::t('Simple registration '),
2 => Yum::t('Confirmation by E-Mail'), 
3 => Yum::t('Confirmation by Admin'),
4=> Yum::t('Confirmation by E-Mail and Admin '),
5 => Yum::t('Auto-generate password '),
6 => Yum::t('Auto-generate password -admin'),

)); ?>
<?php printf('<p class="tooltip">%s</p>', Yum::t(' *** Simple Registration: User is instantly activated after registration <br /> *** Confirmation by Email: Activation link is send to user, and he needs to confirm with this link<br />  *** Confirmation by Admin: Administrator decides which users are accepted and which don\'t.<br /> *** Confirmation by Email and Admin: Administrator sees if E-Mail has already been confirmed or not, but still decides whether to Accept or Decline the User.<br /> *** Auto-Generate password: No password will appear on the registration form, instead it will be auto-generated and emailed to the user.<br /> *** Auto-Generate password -admin: No password will appear on the registration form, instead it will be auto-generated and emailed to the user the admin will activate.
<br /> '));
?>
<?php echo $form->error($model,'enableRegistration'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'enableRecovery'); ?>
<?php echo $form->dropDownList($model,'enableRecovery', array(
0 => Yum::t('Disable recovery'),
1 => Yum::t('Enable recovery')
)); ?>
<?php printf('<p class="tooltip">%s</p>', Yii::t('UserModule.user', 'If enable Recovery is set, registered users will have the possibility to recover their password. The link will be available beneath the login Form.')); ?>
<?php echo $form->error($model,'enableRecovery'); ?>
</div>

<div class="row">
<?php echo $form->labelEx($model,'messageSystem'); ?>
<?php echo $form->dropDownList($model,'messageSystem', array(
			'None' => Yum::t('None'),
			'Plain' => Yum::t('Plain'),
			'Dialog' => Yum::t('Dialog')
			)); ?>
<?php printf('<p class="tooltip">%s</p>', Yii::t('UserModule.user', 'The Message System to use. Plain will use non-Javascript to display new Messages and Dialog will use the CJuiDialog jQuery-plugin to display new Messages')); ?>
<?php echo $form->error($model,'messageSystem'); ?>
</div>

<div class="row">
<?php echo $form->labelEx($model,'notifyType'); ?>
<?php echo $form->dropDownList($model,'notifyType', array(
			'None' => Yum::t('none'),
			'Digest' => Yum::t('daily/weekly digest'),
			'Instant' => Yum::t('one email per message(instant)'),
			'User' => Yum::t('user specific'),
			'Treshhold' => Yum::t('specify treshhold'),
			)); ?>
<?php printf('<p class="tooltip">%s</p>', Yum::t('How should users be notified about new messages? Select digest and read docs/messages_daily_digest on how to configure this feature. Please note that this is the system-wide configuration option. Keep this at \'User\' to let the user choose his preferred way of notification.')); ?>
<?php echo $form->error($model,'notifyType'); ?>
</div>

<div class="row">
<?php echo $form->labelEx($model,'password_expiration_time'); ?>
<?php echo $form->textField($model,'password_expiration_time', array('size' => 5)); ?>
<?php printf('<p class="tooltip">%s</p>', Yum::t('Time in days when the User is forced to change his password. Set to 0 to disable password expiration.')); ?>
<?php echo $form->error($model,'password_expiration_time'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'loginType'); ?>

<?php echo CHtml::activeDropDownList($model, 'loginType', array(
			'LOGIN_BY_USERNAME' => Yii::t('UserModule.user', 'Login allowed only by Username') ,
			'LOGIN_BY_EMAIL' => Yii::t('UserModule.user', 'Login allowed only by Email') ,
			'LOGIN_BY_USERNAME_OR_EMAIL' => Yii::t('UserModule.user', 'Login allowed by Email and Username') ,
)); ?>
<?php printf('<p class="tooltip">%s</p>', Yum::t('This option sets how the login should be allowed')); ?>
<?php echo $form->error($model,'loginType'); ?>
</div>

<div class="row">
<?php echo $form->labelEx($model,'enableCaptcha'); ?>
<?php //printf('<p class="tooltip">%s</p>', Yum::t('Display a Captcha the user needs to enter in the Registration Form?')); ?>
<?php printf('<p class="tooltip">%s</p>', Yum::t('This option sets how the login should be allowed')); ?>
<?php echo $form->dropDownList($model,'enableCaptcha', array(
			0 => Yii::t('UserModule.user', 'No'),
			1 => Yii::t('UserModule.user', 'Yes')
			)); ?>

</div>
<div class="row">
<?php echo $form->labelEx($model,'enableAvatar'); ?>
<?php printf('<p class="tooltip">%s</p>', Yum::t('Allow the upload of an Avatar image for each user')); ?>

<?php echo $form->dropDownList($model,'enableAvatar', array(
			0 => Yum::t('No'),
			1 => Yum::t('Yes')
			)); ?>
<?php echo $form->error($model,'enableAvatar'); ?>
</div>
</div>

<?php
Yum::register('js/tools.tooltip-1.1.3.min.js');
Yum::register('js/tooltip.js');
Yum::register('css/yum.css');?>
