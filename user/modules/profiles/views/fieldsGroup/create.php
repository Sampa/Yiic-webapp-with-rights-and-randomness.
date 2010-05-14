<?php
$this->breadcrumbs=array(
	Yii::t('UserModule.user','Profile fields groups')=>array('admin'),
	Yii::t('UserModule.user','Create'),
);

$this->menu=array(
	YumMenuItemHelper::manageUsers(),
	YumMenuItemHelper::manageFields(),	
);
?>

<h1><?php echo Yii::t("UserModule.user", 'Create profile fields group'); ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
