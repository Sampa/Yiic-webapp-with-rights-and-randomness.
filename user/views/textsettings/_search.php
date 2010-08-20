<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
        'action'=>Yii::app()->createUrl($this->route),
        'method'=>'get',
)); ?>

        <div class="row">
                <?php echo $form->label($model,'id'); ?>
                <?php echo $form->textField($model,'id'); ?>
        </div>

        <div class="row">
                <?php echo $form->label($model,'language'); ?>
                <?php echo CHtml::activeDropDownList($model, 'language', array(
			'en' => Yii::t('app', 'en') ,
			'de' => Yii::t('app', 'de') ,
			'fr' => Yii::t('app', 'fr') ,
			'pl' => Yii::t('app', 'pl') ,
			'ru' => Yii::t('app', 'ru') ,
)); ?>
        </div>

        <div class="row">
                <?php echo $form->label($model,'text_registration_header'); ?>
                <?php echo $form->textArea($model,'text_registration_header',array('rows'=>6, 'cols'=>50)); ?>
        </div>

        <div class="row">
                <?php echo $form->label($model,'text_registration_footer'); ?>
                <?php echo $form->textArea($model,'text_registration_footer',array('rows'=>6, 'cols'=>50)); ?>
        </div>

        <div class="row">
                <?php echo $form->label($model,'text_login_header'); ?>
                <?php echo $form->textArea($model,'text_login_header',array('rows'=>6, 'cols'=>50)); ?>
        </div>

        <div class="row">
                <?php echo $form->label($model,'text_login_footer'); ?>
                <?php echo $form->textArea($model,'text_login_footer',array('rows'=>6, 'cols'=>50)); ?>
        </div>

        <div class="row">
                <?php echo $form->label($model,'text_email_registration'); ?>
                <?php echo $form->textArea($model,'text_email_registration',array('rows'=>6, 'cols'=>50)); ?>
        </div>

        <div class="row">
                <?php echo $form->label($model,'text_email_recovery'); ?>
                <?php echo $form->textArea($model,'text_email_recovery',array('rows'=>6, 'cols'=>50)); ?>
        </div>

        <div class="row">
                <?php echo $form->label($model,'text_email_activation'); ?>
                <?php echo $form->textArea($model,'text_email_activation',array('rows'=>6, 'cols'=>50)); ?>
        </div>

        <div class="row buttons">
                <?php echo CHtml::submitButton(Yii::t('app', 'Search')); ?>
        </div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->
