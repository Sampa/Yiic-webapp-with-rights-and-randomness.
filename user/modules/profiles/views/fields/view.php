<?php
$this->breadcrumbs=array(
	Yii::t("UserModule.user", 'Profile Fields')=>array('admin'),
	Yii::t("UserModule.user", $model->title),
);

$this->menu = array(
		array('label' => Yii::t("UserModule.user", 'Manage User'),
			'url' => array('user/admin')),
		array('label' => Yii::t("UserModule.user", 'Create Profile Field'),
			'url' => array('create')),
		array('label' => Yii::t("UserModule.user", 'Update Profile Field'),
			'url' => array('update')),
		array('label' => Yii::t("UserModule.user", 'Manage Profile Fields'),
			'url' => array('admin')),

		);

?>
<h1><?php echo Yii::t("UserModule.user", 'View Profile Field #').$model->varname; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'varname',
		'title',
		'field_type',
		'field_size',
		'field_size_min',
		'required',
		'match',
		'range',
		'error_message',
		'other_validator',
		'default',
		'position',
		'visible',
	),
)); ?>
