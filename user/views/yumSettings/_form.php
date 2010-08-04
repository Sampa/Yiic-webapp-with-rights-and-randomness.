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

