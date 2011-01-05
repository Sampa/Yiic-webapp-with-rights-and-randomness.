<h2> Paketbestellung </h2>
<div class="form">
<p class="note">
Felder mit * sind Pflichtfelder
</p>

<?php $form=$this->beginWidget('CActiveForm', array(
'id'=>'membership-has-company-form',
	'enableAjaxValidation'=>true,
	)); 
	echo $form->errorSummary($model);
?>


<div class="row">
<?php echo $form->labelEx($model,'membership_id'); ?> <br />
<?php echo CHtml::activeRadioButtonList($model, 'membership_id', 
CHtml::listData(YumRole::model()->findAll('price != 0'), 'id', 'title'),
array('template' => '<div class="checkbox">{input}</div>{label}'));
?>

<br />

<div class="row">
<?php echo $form->labelEx($model,'payment_id'); ?> <br />
<?php echo CHtml::activeRadioButtonList($model, 'payment_id', 
CHtml::listData(YumPayment::model()->findAll(), 'id', 'title'),
array('template' => '<div class="checkbox">{input}</div>{label}'));
?>


<?php echo $form->error($model,'membership_id'); ?>
</div>

<?php
echo CHtml::Button(Yum::t('Cancel'), array(
			'submit' => array('membership/index'))); 
echo CHtml::submitButton(Yum::t('Order membership'));
$this->endWidget(); ?>
</div> <!-- form -->
