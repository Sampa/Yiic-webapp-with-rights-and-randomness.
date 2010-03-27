<?php
$this->breadcrumbs=array(
	Yii::t("UserModule.user", 'Profile Fields'),
);

$this->menu = array(
		array('label' => Yii::t("UserModule.user", 'Create Profile Field'), 
			'url' => array('create')),
		array('label' => Yii::t("UserModule.user", 'Manage Profile Fields'), 
			'url' => array('admin')),
		);


?>

<h1><?php echo Yii::t("UserModule.user", 'List Profile Field'); ?></h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
