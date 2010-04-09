<?php $this->pageTitle=Yii::app()->name . ' - '.
Yii::t("UserModule.user", "Profile");

$this->breadcrumbs=array(
	Yii::t("UserModule.user", "Profile"),
);
?>

<h2><?php echo Yii::t("UserModule.user", 'Your profile'); ?></h2>

<?php

$this->menu=array(
		array(
			'label'=>Yii::t('UserModule.user', 'Manage my Users'),
			'url'=>array('admin'),
			'visible' => Yii::app()->user->hasUsers() &&
			!Yii::app()->user->isAdmin()),
		array(
			'label'=>Yii::t('UserModule.user', 'Manage User'),
			'url'=>array('admin'),
			'visible' => Yii::app()->user->isAdmin()),
		array(
			'label'=>Yii::t('UserModule.user', 'List User'),
			'url'=>array('/user/user/index'),
			'visible' => !Yii::app()->user->isAdmin()),
		array(
			'label'=>Yii::t('UserModule.user', 'Profile'),
			'url'=>array('profile'),
			'visible' => $this->module->hasModule('profiles')),
		array('label'=>Yii::t('UserModule.user', 'Edit'),
			'url'=>array('edit'),
			'visible' => $this->module->hasModule('profiles')),
		array(
			'label'=>Yii::t('UserModule.user', 'Manage Profile Fields'),
			'url'=>array('profiles/fields/admin'),
			'visible' => (Yii::app()->user->isAdmin() 
			&& $this->module->hasModule('profiles'))),
		array(
			'label'=>Yii::t('UserModule.user', 'Manage Roles'),
			'url'=>array('role/role/admin'),
			'visible' => (Yii::app()->user->isAdmin() 
			&& $this->module->hasModule('role'))),
		array(
				'label'=>Yii::t('UserModule.user', 'My Inbox'),
				'url'=>array('messages/messages/index'),
				'visible' => $this->module->hasModule('messages')),
		array(
				'label'=>Yii::t('UserModule.user', 'Compose a Message'),
				'url'=>array('messages/messages/compose'),
				'visible' => $this->module->hasModule('messages')),
		array(
				'label'=>Yii::t('UserModule.user', 'Change password'),
				'url'=>array('changepassword')),
		array('label'=>Yii::t('UserModule.user', 'Logout'),
				'url'=>array('logout')),
		);

?>


<?php
if($this->module->hasModule('messages'))  
		$this->renderPartial('newMessages');
?>

<?php if(Yii::app()->user->hasFlash('profileMessage')): ?>
<div class="success">
<?php echo Yii::app()->user->getFlash('profileMessage'); ?>
</div>
<?php endif; ?>
<table class="dataGrid">
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('username')); ?>
</th>
    <td><?php echo CHtml::encode($model->username); ?>
</td>
</tr>
<?php 
		$profileFields=ProfileField::model()->forOwner()->sort()->findAll();
		if ($profileFields) {
			foreach($profileFields as $field) {
			?>
<tr>
	<th class="label"><?php echo CHtml::encode(Yii::t("UserModule.user", $field->title)); ?>
</th>
    <td><?php echo CHtml::encode($profile->getAttribute($field->varname)); ?>
</td>
</tr>
			<?php
			}
		}
?>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('password')); ?>
</th>
    <td><?php echo CHtml::link(Yii::t("UserModule.user", "Change password"),array("changepassword")); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('createtime')); ?>
</th>
    <td><?php echo date(UserModule::$dateFormat,$model->createtime); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('lastvisit')); ?>
</th>
    <td><?php echo date(UserModule::$dateFormat,$model->lastvisit); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('status')); ?>
</th>
    <td><?php echo CHtml::encode(User::itemAlias("UserStatus",$model->status));
    ?>
</td>
</tr>
</table>
