<?php
$this->breadcrumbs=array(
		Yii::t("UserModule.user", 'Roles')=>array('index'),
		Yii::t("UserModule.user", 'Manage'),
		);

$this->menu = array(
	YumMenuItemHelper::createRole(),
	YumMenuItemHelper::manageUsers()
)
?>

<h1> <?php echo Yii::t('UserModule.user', 'Manage Roles'); ?> </h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
			'name' => 'id',
			'type' => 'raw',
			'value'=> 'CHtml::link(CHtml::encode($data->id),
				array(YumHelper::route("{roles}/role/update"),"id"=>$data->id))',
		),
		array(
			'name' => 'title',
			'type' => 'raw',
			'value'=> 'CHtml::link(CHtml::encode($data->title),
				array(YumHelper::route("{roles}/role/view"),"id"=>$data->id))',
		),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
