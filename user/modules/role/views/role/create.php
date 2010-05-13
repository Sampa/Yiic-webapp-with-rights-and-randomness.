<?php
$this->breadcrumbs=array(
	Yii::t("UserModule.user", 'Roles')=>array('index'),
	Yii::t("UserModule.user", 'Create'),
);

$this->menu = array(
	YumMenuItemHelper::manageRoles(),
	YumMenuItemHelper::manageUsers(),
);
?>
<h1><?php echo Yii::t("UserModule.user", "Create Role"); ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
