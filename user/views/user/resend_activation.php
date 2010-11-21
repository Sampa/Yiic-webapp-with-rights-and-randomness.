<?php 
$this->pageTitle=Yii::app()->name . ' - '.Yii::t("UserModule.user", "Activate");

$this->breadcrumbs=array(
	Yii::t("UserModule.user", "Login") => array(Yum::route('{user}/login')),
	Yii::t("UserModule.user", "Activate"));

$this->title = Yii::t("UserModule.user", "Activate"); 
?>

<?php 
if(Yii::app()->user->hasFlash('registration'))
{ 
?>
<div class="success">
<?php echo Yii::app()->user->getFlash('registration'); ?>
</div>
<?php 
}
?>

<div class="form">
<?php echo CHtml::beginForm(array('registration/activation'),'GET',array()); ?> 

<div id="activatiion_code">
<div class="row">
<?php echo Yii::t("UserModule.user", "Enter the activation code you recieved below."); ?>
</div>
<div class="row">
<?php if(isset($form->email)){ 
echo CHtml::hiddenField('email',$form->email);  
 }else{ 
	echo CHtml::activeLabelEx($user,'email');
	echo CHtml::textField('email',$form->email);
		}
?>

<?php echo CHtml::activeLabelEx($user,'activationKey'); ?>
<?php echo CHtml::textField('activationKey'); //fixme ?> 
</div>
<div class="row_submit">
<?php echo CHtml::submitButton(Yii::t("UserModule.user", "Activate")); ?>
</div>
</div>
<?php echo CHtml::endForm(); ?>
</div><!-- form -->

<div class="form">
<?php echo CHtml::beginForm(array('registration/ResendActivation'),'POST',array()); ?>
	
	<div id="email">
	<?php if(isset($form->email)){ 
echo CHtml::hiddenField('email',$form->email);  
 }else{ 
	echo CHtml::activeLabelEx($user,'email');
	echo CHtml::textField('email',$form->email);
		}
?>
	</div>
	<div id="resend_activation_text">
	<?php echo Yii::t("UserModule.user", "If you failed to recieve the activation email, click the Resend Activation button."); ?>
	</div>
	<div class="row_submit">
		<?php echo CHtml::submitButton(Yii::t("UserModule.user", "Resend Activation")); ?>
	</div>

<?php echo CHtml::endForm(); ?>
</div><!-- form -->

