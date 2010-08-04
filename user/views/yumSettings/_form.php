<p class="note"><?php echo Yii::t('app','Fields with');?> <span class="required">*</span> <?php echo Yii::t('app','are required');?>.</p>

<?php echo $form->errorSummary($model); ?>

		<div class="row">
<?php echo $form->labelEx($model,'title'); ?>
<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>255)); ?>
<?php echo $form->error($model,'title'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'preserveProfiles'); ?>
<?php echo $form->dropDownList($model,'preserveProfiles', array(
0 => Yii::t('UserModule.user', 'No'),
1 => Yii::t('UserModule.user', 'Yes')
)); ?>
<?php echo $form->error($model,'preserveProfiles'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'enableRegistration'); ?>
<?php echo $form->dropDownList($model,'enableRegistration', array(
0 => Yii::t('UserModule.user', 'No'),
1 => Yii::t('UserModule.user', 'Yes')
)); ?>
<?php echo $form->error($model,'enableRegistration'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'enableRecovery'); ?>
<?php echo $form->dropDownList($model,'enableRecovery', array(
0 => Yii::t('UserModule.user', 'No'),
1 => Yii::t('UserModule.user', 'Yes')
)); ?>
<?php echo $form->error($model,'enableRecovery'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'enableEmailActivation'); ?>
<?php echo $form->dropDownList($model,'enableEmailActivation', array(
0 => Yii::t('UserModule.user', 'No'),
1 => Yii::t('UserModule.user', 'Yes')
)); ?>
<?php echo $form->error($model,'enableEmailActivation'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'enableProfileHistory'); ?>
<?php echo $form->dropDownList($model,'enableProfileHistory', array(
0 => Yii::t('UserModule.user', 'No'),
1 => Yii::t('UserModule.user', 'Yes')
)); ?>
<?php echo $form->error($model,'enableProfileHistory'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'readOnlyProfiles'); ?>
<?php echo $form->dropDownList($model,'readOnlyProfiles', array(
0 => Yii::t('UserModule.user', 'No'),
1 => Yii::t('UserModule.user', 'Yes')
)); ?>

<?php echo $form->error($model,'readOnlyProfiles'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'loginType'); ?>
<?php echo CHtml::activeDropDownList($model, 'loginType', array(
			'LOGIN_BY_USERNAME' => Yii::t('UserModule.user', 'Login allowed only by Username') ,
			'LOGIN_BY_EMAIL' => Yii::t('UserModule.user', 'Login allowed only by Email') ,
			'LOGIN_BY_USERNAME_OR_EMAIL' => Yii::t('UserModule.user', 'Login allowed my Email and Username') ,
)); ?>
<?php echo $form->error($model,'loginType'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'enableCaptcha'); ?>
<?php echo $form->dropDownList($model,'enableCaptcha', array(
0 => Yii::t('UserModule.user', 'No'),
1 => Yii::t('UserModule.user', 'Yes')
)); ?>


<?php echo $form->error($model,'enableCaptcha'); ?>
</div>

