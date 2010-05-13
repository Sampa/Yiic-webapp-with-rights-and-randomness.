<?php
$this->breadcrumbs=array(
	Yii::t("UserModule.user", 'Profile Fields')=>array('admin'),
	Yii::t("UserModule.user", $model->title),
);

$this->menu = array(
	YumMenuItemHelper::manageUsers(),
	YumMenuItemHelper::manageFields(),	
	YumMenuItemHelper::createField(),
	YumMenuItemHelper::updateField(),
	YumMenuItemHelper::manageFieldsGroups(),	
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
