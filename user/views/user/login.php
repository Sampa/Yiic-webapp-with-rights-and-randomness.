<?php
if(!isset($model)) 
	$model = new YumUserLogin();
$this->pageTitle = Yii::app()->name . ' - '.Yii::t("UserModule.user", "Login");
$this->title = Yii::t("UserModule.user", "Login");
$this->breadcrumbs=array(Yii::t('user', 'Login'));
$module = $this->getModule('user');

if(Yii::app()->user->hasFlash('loginMessage')): ?>

<div class="success">
	<?php echo Yii::app()->user->getFlash('loginMessage'); ?>
</div>

<?php endif; ?>
<?php
if($this->module->tableSettingsDisabled != true) {
	$settings = YumTextSettings::model()->find("language = :language", array(
				':language' => Yii::app()->language));

	if($settings) 
		printf('%s<br /><br />', $settings->text_login_header);
}
?>

<p>
<?php 
echo Yum::t("Please fill out the following form with your login credentials:"); ?></p>

<div class="form">
<?php echo CHtml::beginForm(); ?>

	<?php echo Yum::requiredFieldNote(); ?>
	<?php echo CHtml::errorSummary($model); ?>
	
	<div class="row">
		<?php 
		if($module->loginType == 'LOGIN_BY_EMAIL')
			printf ('<label for="username">%s</label>', Yum::t('E-Mail address'));
		else
			echo CHtml::activeLabelEx($model,'username'); ?>
		<?php echo CHtml::activeTextField($model,'username') ?>
	</div>
	
	<div class="row">
		<?php echo CHtml::activeLabelEx($model,'password'); ?>
		<?php echo CHtml::activePasswordField($model,'password') ?>
	</div>
	
	<div class="row">
	<p class="hint">
	<?php 
if($module->registrationType != YumRegistration::REG_DISABLED)
	echo CHtml::link(Yum::t("Registration"), $this->module->registrationUrl);
	if($module->registrationType != YumRegistration::REG_DISABLED 
			&& $module->allowRecovery)
	echo ' | ';
if($module->allowRecovery)
	echo CHtml::link(Yum::t("Lost password?"), $this->module->recoveryUrl);
	?>
	</p>
</div>

<div class="row rememberMe">
<?php echo CHtml::activeCheckBox($model,'rememberMe'); ?>
<?php echo CHtml::activeLabelEx($model,'rememberMe'); ?>
</div>
<?php
	if($this->module->tableSettingsDisabled != true) {
		if($settings) 
			printf('%s<br /><br />', $settings->text_login_footer);
	}
?>

<div class="row submit">
<?php echo CHtml::submitButton(Yii::t("UserModule.user", "Login")); ?>
</div>

<?php echo CHtml::endForm(); ?>
</div><!-- form -->


<?php
$form = new CForm(array(
			'elements'=>array(
				'username'=>array(
					'type'=>'text',
					'maxlength'=>32,
					),
				'password'=>array(
					'type'=>'password',
					'maxlength'=>32,
					),
				'rememberMe'=>array(
					'type'=>'checkbox',
					)
				),

			'buttons'=>array(
				'login'=>array(
					'type'=>'submit',
					'label'=>'Login',
					),
				),
			), $model);
?>
