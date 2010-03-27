<?php
$this->breadcrumbs=array(
		Yii::t("UserModule.user", 'Profile Fields')=>array('admin'),
		$model->title=>array('view','id'=>$model->id),
		Yii::t("UserModule.user", 'Update'),
		);

$this->menu = array(
		array('label' => Yii::t("UserModule.user", 'Manage User'),
			'url' => array('/user/user/admin')),
		array('label' => Yii::t("UserModule.user", 'Create Profile Field'),
			'url' => array('create')),
		array('label' => Yii::t("UserModule.user", 'Manage Profile Fields'),
			'url' => array('admin')),
		);

?>

<h1><?php echo Yii::t("UserModule.user", 'Update Profile Field'). ' ' . $model->varname; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
