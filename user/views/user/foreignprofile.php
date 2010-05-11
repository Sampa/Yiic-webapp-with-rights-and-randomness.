<?php 
$this->pageTitle=Yii::app()->name . ' - '.
Yii::t("UserModule.user", "Profile");

$this->breadcrumbs=array(
		Yii::t("UserModule.user", "Profile"), $model->username,); 

$this->menu=array(
		array('label'=>Yii::t('UserModule.user', 'Back to my Profile'),
			'url'=>array('profile')
			),
		array('label'=>Yii::t('UserModule.user', 'Logout'),
			'url'=>array('logout')
			),
		array(
			'label'=>Yii::t('UserModule.user', 'Send a message to this User'),
			'url'=>array('messages/messages/compose', 'to_user_id' => $model->id),
			'visible' => $this->module->hasModule('messages')
			),
		);
?>

<h2><?php echo Yii::t("UserModule.user", 'Profile of ') . $model->username; ?> 
</h2> 


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
		$profileFields=YumProfileField::model()->forOwner()->sort()->findAll();
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
</table>
