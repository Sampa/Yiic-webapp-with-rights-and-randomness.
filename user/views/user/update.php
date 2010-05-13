<?php
$this->breadcrumbs=array(
	Yii::t("UserModule.user", 'Users')=>array('index'),
	$model->username=>array('view','id'=>$model->id),
	Yii::t("UserModule.user", 'Update'),
);
$this->menu = array(
	YumMenuItemHelper::manageUsers(),
	YumMenuItemHelper::listUsers(),
	YumMenuItemHelper::createUser(),
	YumMenuItemHelper::viewUser(array('id'=>$model->id)),
	YumMenuItemHelper::manageRoles(),
	YumMenuItemHelper::updateProfile(array('id'=>$model->id),'Manage this profile'),
	YumMenuItemHelper::manageFields()
);
?>

<h1><?php echo Yii::t("UserModule.user", 'Update User')." ".$model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model,'profile'=>$profile)); ?>
