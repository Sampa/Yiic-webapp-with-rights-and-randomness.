<?php
$this->breadcrumbs=array(
		Yii::t("UserModule.user", 'Profile Fields')=>array('admin'),
		$model->title=>array('view','id'=>$model->id),
		Yii::t("UserModule.user", 'Update'),
		);

$this->menu = array(
	YumMenuItemHelper::manageUsers(),
	YumMenuItemHelper::manageFields(),
	YumMenuItemHelper::manageFieldsGroups(),
);
?>

<h1><?php echo Yii::t("UserModule.user", 'Update Profile Field'). ' ' . $model->varname; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
