<?php
$this->breadcrumbs=array(
		Yii::t("UserModule.user", "Users"),
		);
?>

<?php
$this->menu = array(
		array('label'=>Yii::t('UserModule.user', 'Create User'),
			'url'=>array('create'),
			'visible' => Yii::app()->user->isAdmin(),
			),
		array('label'=>Yii::t('UserModule.user', 'Manage User'),
			'url'=>array('admin'),
			'visible' => Yii::app()->user->isAdmin()
			),
		array('label'=>Yii::t('UserModule.user', 'Manage profile Fields'),
			'url' =>array('profiles/fields/admin'),
			'visible' => $this->module->hasModule('profiles')
			&& Yii::app()->user->isAdmin()
			),
		array('label'=>Yii::t('UserModule.user', 'Manage Roles'),
			'url'=>array('role/role/admin'),
			'visible' => $this->module->hasModule('role')
			&& Yii::app()->user->isAdmin())
		);

?>


	<?php
if(Yii::app()->controller->module->debug === true) 
{
	echo	CHtml::openTag('div', array('class' => 'hint'));
	echo 'You are running the Yii User Management Module ' .
		Yii::app()->controller->module->version .
		' in Debug Mode!';
	echo CHtml::closeTag('div'); 
}

?>

<h1> <?php echo Yii::t('UserModule.user', 'Users: '); ?></h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
			'dataProvider'=>$dataProvider,
			'columns'=>array(
		array(
			'name' => 'username',
			'type'=>'raw',
			'value' => 'CHtml::link(CHtml::encode($data->username),
				array("user/profile","id"=>$data->id))',
			),
		array(
			'name' => 'createtime',
			'value' => 'date(UserModule::$dateFormat,$data->createtime)',
		),
		array(
			'name' => 'lastvisit',
			'value' => 'date(UserModule::$dateFormat,$data->lastvisit)',
		),
	),
)); ?>


