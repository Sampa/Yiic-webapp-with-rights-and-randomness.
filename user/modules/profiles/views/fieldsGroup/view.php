<?php
$this->breadcrumbs=array(
	Yii::t('UserModule.user','Profile fields groups') => array('admin'),
	Yii::t('UserModule.user',$model->title)
);

$this->menu=array(
	YumMenuItemHelper::manageUsers(),
	YumMenuItemHelper::manageFields(),
	YumMenuItemHelper::createFieldsGroup(),
	YumMenuItemHelper::updateFieldsGroup(array('id'=>$model->id)),
	YumMenuItemHelper::manageFieldsGroups()
);
?>

<h1><?php echo Yii::t("UserModule.user", 'View profile fields group #').$model->group_name; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'group_name',
		'title',
		'position',
	),
)); ?>
