<?php
$this->breadcrumbs=array(
	Yii::t("UserModule.user", 'Users')=>array('index'),
	Yii::t("UserModule.user", 'Manage'),
);
?>
	<h1><?php echo Yii::t('UserModule.user', 'Manage Users'); ?></h1>

<?php $this->menu = array(
	YumMenuItemHelper::listUsers(),
	YumMenuItemHelper::createUser(),
	YumMenuItemHelper::manageRoles(),
	YumMenuItemHelper::manageFields()
);
?>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$dataProvider,
		'columns'=>array(
			array(
				'name'=>'id',
				'type'=>'raw',
				'value'=>'CHtml::link(CHtml::encode($data->id),
				array(YumHelper::route("{user}/update"),"id"=>$data->id))',
			),
			array(
				'name'=>'username',
				'type'=>'raw',
				'value'=>'CHtml::link(CHtml::encode($data->username),
				array(YumHelper::route("{user}/view"),"id"=>$data->id))',
			),
			array(
				'name'=>'createtime',
				'value'=>'date(UserModule::$dateFormat,$data->createtime)',
			),
			array(
				'name'=>'lastvisit',
				'value'=>'date(UserModule::$dateFormat,$data->lastvisit)',
			),
			array(
				'name'=>'status',
				'value'=>'YumUser::itemAlias("UserStatus",$data->status)',
			),
			array(
				'name'=>'superuser',
				'value'=>'YumUser::itemAlias("AdminStatus",$data->superuser)',
			),
			array(
				'class'=>'CButtonColumn',
			),
))); ?>
