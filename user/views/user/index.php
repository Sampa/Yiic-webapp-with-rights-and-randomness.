<?php $this->breadcrumbs=array(Yii::t("UserModule.user", "Users"));?>

<?php $this->menu = array(
	YumMenuItemHelper::createUser(),
	YumMenuItemHelper::manageUsers(),
	YumMenuItemHelper::manageFields(),
	YumMenuItemHelper::manageRoles()
); ?>


<?php if(Yii::app()->controller->module->debug === true) 
{
	echo CHtml::openTag('div', array('class' => 'hint'));
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
				array(YumHelper::route("{user}/user/profile"),"id"=>$data->id))',
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


