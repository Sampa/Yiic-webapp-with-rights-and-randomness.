<?php
$this->breadcrumbs=array(
	Yii::t("UserModule.user", 'Profile Fields')=>array('admin'),
	Yii::t("UserModule.user", 'Create'),
);

$this->menu = array(
	YumMenuItemHelper::manageUsers(),
	YumMenuItemHelper::manageFields(),
	YumMenuItemHelper::manageFieldsGroups(),	
);
?>
<h1><?php echo Yii::t("UserModule.user", 'Create Profile Field'); ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
