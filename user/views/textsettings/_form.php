<p class="note"><?php echo Yii::t('app','Fields with');?> <span class="required">*</span> <?php echo Yii::t('app','are required');?>.</p>


<?php if(isset($_POST['returnUrl']))

		echo CHtml::hiddenField('returnUrl', $_POST['returnUrl']); ?>
<?php echo $form->errorSummary($model); ?>

		<div class="row">
<?php echo $form->labelEx($model,'language'); ?>
<?php echo CHtml::activeDropDownList($model, 'language', array(
			'en' => Yii::t('app', 'en') ,
			'de' => Yii::t('app', 'de') ,
			'fr' => Yii::t('app', 'fr') ,
			'pl' => Yii::t('app', 'pl') ,
			'ru' => Yii::t('app', 'ru') ,
)); ?>
<?php echo $form->error($model,'language'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'text_registration_header'); ?>
<?php echo $form->textArea($model,'text_registration_header',array('rows'=>6, 'cols'=>50)); ?>
<?php echo $form->error($model,'text_registration_header'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'text_registration_footer'); ?>
<?php echo $form->textArea($model,'text_registration_footer',array('rows'=>6, 'cols'=>50)); ?>
<?php echo $form->error($model,'text_registration_footer'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'text_login_header'); ?>
<?php echo $form->textArea($model,'text_login_header',array('rows'=>6, 'cols'=>50)); ?>
<?php echo $form->error($model,'text_login_header'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'text_login_footer'); ?>
<?php echo $form->textArea($model,'text_login_footer',array('rows'=>6, 'cols'=>50)); ?>
<?php echo $form->error($model,'text_login_footer'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'text_email_registration'); ?>
<?php echo $form->textArea($model,'text_email_registration',array('rows'=>6, 'cols'=>50)); ?>
<?php echo $form->error($model,'text_email_registration'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'text_email_recovery'); ?>
<?php echo $form->textArea($model,'text_email_recovery',array('rows'=>6, 'cols'=>50)); ?>
<?php echo $form->error($model,'text_email_recovery'); ?>
</div>

		<div class="row">
<?php echo $form->labelEx($model,'text_email_activation'); ?>
<?php echo $form->textArea($model,'text_email_activation',array('rows'=>6, 'cols'=>50)); ?>
<?php echo $form->error($model,'text_email_activation'); ?>
</div>

