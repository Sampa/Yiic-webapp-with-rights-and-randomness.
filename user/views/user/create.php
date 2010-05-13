<?php
$this->breadcrumbs=array(
	Yii::t('UserModule.user', 'Users')=>array('index'),
	Yii::t('UserModule.user', 'Create'),
);

$this->menu = array(
	YumMenuItemHelper::listUsers(),
	YumMenuItemHelper::manageUsers(),
	YumMenuItemHelper::manageFields()
);
?>
<h1><?php echo Yii::t('UserModule.user', "Create User"); ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'profile'=>$profile)); ?>
