<p class="note"><?php echo Yii::t('app','Fields with');?> <span class="required">*</span> <?php echo Yii::t('app','are required');?>.</p>

<?php echo $form->errorSummary($model); ?>

		<div class="row">
<?php echo $form->labelEx($model,'title'); ?>
<?php printf('<p class="hint">%s</p>', Yii::t('UserModule.user', 'Title of this settings profile')); ?>
<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
<?php echo $form->error($model,'title'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'preserveProfiles'); ?>
<?php printf('<p class="hint">%s</p>', Yii::t('UserModule.user', 'If preserveProfiles is set, the profiles are not being removed then the user gets deleted. This way the administrator keeps his user profile history forever')); ?>
<?php echo $form->dropDownList($model,'preserveProfiles', array(
0 => Yii::t('UserModule.user', 'No'),
1 => Yii::t('UserModule.user', 'Yes')
)); ?>
<?php echo $form->error($model,'preserveProfiles'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'enableRegistration'); ?>
<?php printf('<p class="hint">%s</p>', Yii::t('UserModule.user', 'If enable Registration is set, users are able to register his own account. The link will be available beneath the login Form.')); ?>
<?php echo $form->dropDownList($model,'enableRegistration', array(
0 => Yii::t('UserModule.user', 'No'),
1 => Yii::t('UserModule.user', 'Yes')
)); ?>
<?php echo $form->error($model,'enableRegistration'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'enableRecovery'); ?>
<?php printf('<p class="hint">%s</p>', Yii::t('UserModule.user', 'If enable Recovery is set, registerd users will have the possibility to recover his own password. The link will be available beneath the login Form.')); ?>

<?php echo $form->dropDownList($model,'enableRecovery', array(
0 => Yii::t('UserModule.user', 'No'),
1 => Yii::t('UserModule.user', 'Yes')
)); ?>
<?php echo $form->error($model,'enableRecovery'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'enableEmailActivation'); ?>
<?php printf('<p class="hint">%s</p>', Yii::t('UserModule.user', 'If enable Email Activation is set, a user needs to confirm his account by an Activation Email that is send out to the user after the Registration process. If the link gets confirmed, his account will be set to status active.')); ?>

<?php echo $form->dropDownList($model,'enableEmailActivation', array(
0 => Yii::t('UserModule.user', 'No'),
1 => Yii::t('UserModule.user', 'Yes')
)); ?>
<?php echo $form->error($model,'enableEmailActivation'); ?>
</div>

<div class="row">
<?php echo $form->labelEx($model,'messageSystem'); ?>
<?php printf('<p class="hint">%s</p>', Yii::t('UserModule.user', 'The Message System to use. Plain will use non-Javascript to display new Messages and Dialog will use the CJuiDialog jQuery-plugin to display new Messages')); ?>

<?php echo $form->dropDownList($model,'messageSystem', array(
			'None' => Yum::t('None'),
			'Plain' => Yum::t('Plain'),
			'Dialog' => Yum::t('Dialog')
			)); ?>
<?php echo $form->error($model,'messageSystem'); ?>
</div>

<div class="row">
<?php echo $form->labelEx($model,'mail_send_method'); ?>
<?php echo $form->dropDownList($model,'mail_send_method', array(
			'Disabled' => Yum::t('Disable email sending'),
			'Daily' => Yum::t('Daily summary of new Messages (if any)'),
			'Message' => Yum::t('One E-mail per Message (instant)')
			)); ?>
<?php printf('<p class="hint">%s</p>', Yii::t('UserModule.user', 'If activated, the System tries to inform every user about ew Messages that have been sent. Can be set to daily Summary or to one E-Mail per message')); ?>
<?php echo $form->error($model,'auto_mail_sending'); ?>
</div>

<div class="row">
<?php echo $form->labelEx($model,'password_expiration_time'); ?>
<?php printf('<p class="hint">%s</p>', Yii::t('UserModule.user', 'Time in days when the User is forced to change his password. Set to 0 to disable password expiration.')); ?>
<?php echo $form->textField($model,'password_expiration_time', array('size' => 5)); ?>
<?php echo $form->error($model,'password_expiration_time'); ?>
</div>



		<div class="row">
<?php echo $form->labelEx($model,'enableProfileHistory'); ?>
<?php printf('<p class="hint">%s</p>', Yii::t('UserModule.user', 'If enable History is set, user profiles are kept in the System if the user changes his profile Data. This way the admin is able to see what has changed in the profile history log.')); ?>

<?php echo $form->dropDownList($model,'enableProfileHistory', array(
0 => Yii::t('UserModule.user', 'No'),
1 => Yii::t('UserModule.user', 'Yes')
)); ?>
<?php echo $form->error($model,'enableProfileHistory'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'readOnlyProfiles'); ?>
<?php printf('<p class="hint">%s</p>', Yii::t('UserModule.user', 'Should users be able to update their profile data?')); ?>

<?php echo $form->dropDownList($model,'readOnlyProfiles', array(
0 => Yii::t('UserModule.user', 'No'),
1 => Yii::t('UserModule.user', 'Yes')
)); ?>

<?php echo $form->error($model,'readOnlyProfiles'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'loginType'); ?>
<?php printf('<p class="hint">%s</p>', Yii::t('UserModule.user', 'This option sets how the login should be allowed')); ?>

<?php echo CHtml::activeDropDownList($model, 'loginType', array(
			'LOGIN_BY_USERNAME' => Yii::t('UserModule.user', 'Login allowed only by Username') ,
			'LOGIN_BY_EMAIL' => Yii::t('UserModule.user', 'Login allowed only by Email') ,
			'LOGIN_BY_USERNAME_OR_EMAIL' => Yii::t('UserModule.user', 'Login allowed my Email and Username') ,
)); ?>
<?php echo $form->error($model,'loginType'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'enableCaptcha'); ?>
<?php printf('<p class="hint">%s</p>', Yii::t('UserModule.user', 'Display a Captcha the user needs to enter in the Registration Form?')); ?>

<?php echo $form->dropDownList($model,'enableCaptcha', array(
0 => Yii::t('UserModule.user', 'No'),
1 => Yii::t('UserModule.user', 'Yes')
)); ?>


<?php echo $form->error($model,'enableCaptcha'); ?>
</div>

