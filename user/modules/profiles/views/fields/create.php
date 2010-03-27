<?php
$this->breadcrumbs=array(
	Yii::t("UserModule.user", 'Profile Fields')=>array('admin'),
	Yii::t("UserModule.user", 'Create'),
);

$this->menu = array(
		array('label' => Yii::t("UserModule.user", 'Manage User'),
			'url' => array('/user/user/admin')),
		array('label' => Yii::t("UserModule.user", 'Manage Profile Fields'),
			'url' => array('admin')),
		);

?>
<h1><?php echo Yii::t("UserModule.user", 'Create Profile Field'); ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
