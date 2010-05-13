<?php
$this->breadcrumbs=array(
	Yii::t("UserModule.user", 'Profile Fields'),
);

$this->menu = array(
	YumMenuItemHelper::createField(),
	YumMenuItemHelper::manageFields(),
	YumMenuItemHelper::manageFieldsGroups(),	
);
?>

<h1><?php echo Yii::t("UserModule.user", 'List Profile Field'); ?></h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
