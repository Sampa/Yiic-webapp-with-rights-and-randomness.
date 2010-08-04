<?php
$this->title = Yii::t('UserModule.user', 'View user "{username}"',array(
			'{username}'=>$model->username));

$this->breadcrumbs=array(Yii::t('UserModule.user', 'Users') => array('index'), $model->username);

$this->menu = array(
		YumMenuItemHelper::adminPanel(), 
		YumMenuItemHelper::manageUsers(),
		YumMenuItemHelper::createUser(),
		YumMenuItemHelper::updateUser(array('id'=>$model->id)),
		YumMenuItemHelper::manageFields(),
		YumMenuItemHelper::manageRoles());
?>

<?php 
if(Yii::app()->user->isAdmin()) {
	$attributes = array(
			'id',
			'username',
	);
	
	$profileFields = YumProfileField::model()->forOwner()->sort()->findAll();
	if ($profileFields && $model->profile) {
		foreach($profileFields as $field) {
			array_push($attributes, array(
				'label' => Yii::t('UserModule.user', $field->title),
				'name' => $field->varname,
				'value' => is_array($model->profile) 
				? $model->profile[0]->getAttribute($field->varname) 
				: $model->profile->getAttribute($field->varname) ,
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
					'label' => Yii::t('UserModule.user', $field->title),
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
if(Yii::app()->controller->module->profileHistory) 
{

$this->renderPartial('/profile/profile_history', array('model' => $model));
}

?>

<?php 
	echo Yii::t('UserModule.user', 'This user belongs to these roles:');  

	if($model->roles) {
		echo "<ul>";
		foreach($model->roles as $role) {
			echo CHtml::tag('li',array(),CHtml::link(
						$role->title,array(YumHelper::route('role/view'),'id'=>$role->id)),true);
		}
		echo "</ul>";
	} else {
		printf('<p>%s</p>', Yii::t('UserModule.user', 'None'));
	}

echo '<hr />';

echo Yii::t('UserModule.user', 'This user can administer this users:');  

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
	printf('<p>%s</p>', Yii::t('UserModule.user', 'None'));
}

?>
