<?php
$this->title = Yii::t('UserModule.user', 'Manage roles'); 

$this->breadcrumbs=array(
	Yii::t("UserModule.user", 'Roles')=>array('index'),
	Yii::t("UserModule.user", 'Manage'),
);

?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
	'columns'=>array(
		array(
			'name' => 'id',
			'type' => 'raw',
			'value'=> 'CHtml::link(CHtml::encode($data->id),
				array(Yum::route("role/update"),"id"=>$data->id))',
		),
		array(
			'name' => 'title',
			'type' => 'raw',
			'value'=> 'CHtml::link(CHtml::encode($data->title),
				array(Yum::route("role/view"),"id"=>$data->id))',
		),
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
