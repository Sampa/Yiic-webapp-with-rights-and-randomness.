<?php
$this->breadcrumbs=array(
	Yii::t("UserModule.user", 'Users')=>array('index'),
	$model->username,
);
?>

<?php
$this->menu = array(
	YumMenuItemHelper::manageUsers(),
	YumMenuItemHelper::listUsers(),
	YumMenuItemHelper::createUser(),
	YumMenuItemHelper::updateUser(array('id'=>$model->id)),
	YumMenuItemHelper::manageFields(),
	YumMenuItemHelper::manageRoles(),
);
?>

<h1><?php echo Yii::t("UserModule.user", 'View User').' "'.$model->username.'"'; ?></h1>

<?php 
if(Yii::app()->user->isAdmin()) {
	$attributes = array(
		'id',
		'username',
	);
	
	$profileFields=YumProfileField::model()->forOwner()->sort()->findAll();
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
			'value' => YumUser::itemAlias("AdminStatus",$model->superuser),
		),
		array(
			'name' => 'status',
			'value' => YumUser::itemAlias("UserStatus",$model->status),
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
	
	$profileFields=YumProfileField::model()->forAll()->sort()->findAll();
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
		echo CHtml::tag('li',array(),CHtml::link(
			$role->title,array(YumHelper::route('{roles}/role/view'),'id'=>$role->id)),true);
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
		echo CHtml::tag('li',array(),CHtml::link(
			$user->username,array(YumHelper::route('{user}/view'),'id'=>$user->id)),true);		
	}
	echo "</ul>";
}
else 
{
	echo '<p> None </p>';
}


