<?php
$this->breadcrumbs=array(
	Yii::t("UserModule.user", 'Users')=>array('index'),
	$model->username,
);
?>

<?php
$this->menu = array(
		array('label'=>Yii::t('UserModule.user', 'List User'),
			'url'=>array('index')
			),
		array('label'=>Yii::t('UserModule.user', 'Create User'),
			'url'=>array('create')
			),
		array('label'=>Yii::t('UserModule.user', 'Update User'),
			'url'=>array('update','id'=>$model->id)
			),
		array('label'=>Yii::t('UserModule.user', 'Manage User'),
			'url'=>array('admin')
			),
		array('label'=>Yii::t('UserModule.user', 'Manage profile Fields'),
			'url'=>array('profiles/fields/admin'),
			'visible' => $this->module->hasModule('profiles')
			),
		array('label'=>Yii::t('UserModule.user', 'Manage Roles'),
			'url'=>array('role/role/admin'),
			'visible' => $this->module->hasModule('role'))
		); 

?>

<h1><?php echo Yii::t("UserModule.user", 'View User').' "'.$model->username.'"'; ?></h1>

<?php 
if(Yii::app()->user->isAdmin()) {
	$attributes = array(
		'id',
		'username',
	);
	
	$profileFields=ProfileField::model()->forOwner()->sort()->findAll();
	if ($profileFields && $model->profile) 
	{
		foreach($profileFields as $field) 
		{
			array_push($attributes,array(
						'label' => Yii::t("UserModule.user", $field->title),
						'name' => $field->varname,
						'value' => $model->profile->getAttribute($field->varname),
						));
		}
	}

	array_push($attributes,
		'password',
		'activationKey',
		array(
			'name' => 'createtime',
			'value' => date(UserModule::$dateFormat,$model->createtime),
		),
		array(
			'name' => 'lastvisit',
			'value' => date(UserModule::$dateFormat,$model->lastvisit),
		),
		array(
			'name' => 'superuser',
			'value' => User::itemAlias("AdminStatus",$model->superuser),
		),
		array(
			'name' => 'status',
			'value' => User::itemAlias("UserStatus",$model->status),
		)
	);
	
	$this->widget('zii.widgets.CDetailView', array(
		'data'=>$model,
		'attributes'=>$attributes,
	));
	
} else {
// For all users
	$attributes = array(
			'username',
	);
	
	$profileFields=ProfileField::model()->forAll()->sort()->findAll();
	if ($profileFields) {
		foreach($profileFields as $field) {
			array_push($attributes,array(
					'label' => Yii::t("UserModule.user", $field->title),
					'name' => $field->varname,
					'value' => $model->profile->getAttribute($field->varname),
				));
		}
	}
	array_push($attributes,
		array(
			'name' => 'createtime',
			'value' => date(UserModule::$dateFormat,$model->createtime),
		),
		array(
			'name' => 'lastvisit',
			'value' => date(UserModule::$dateFormat,$model->lastvisit),
		)
	);
			
	$this->widget('zii.widgets.CDetailView', array(
		'data'=>$model,
		'attributes'=>$attributes,
	));
}
?>

<hr />

<?php 

echo Yii::t('UserModule.User', 'This User belongs to these roles:');  ?>

<?php 
if($model->roles) {
	echo "<ul>";
	foreach($model->roles as $role) {
		printf("<li>%s</li>", CHtml::link($role->title,
					array('role/view',
						'id' => $role->id)));
	}
	echo "</ul>";
}
else 
{
	echo '<p> None </p>';
}

?>

<hr />

<?php

echo Yii::t('UserModule.User', 'This User can administrate this Users:');  ?>

<?php 
if($model->users) {
	echo "<ul>";
	foreach($model->users as $user) {
		printf("<li>%s</li>",
				CHtml::link($user->username,
					array('user/view', 'id' => $user->id)));
	}
	echo "</ul>";
}
else 
{
	echo '<p> None </p>';
}


