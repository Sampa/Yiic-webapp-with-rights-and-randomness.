<?php
$this->pageTitle=Yii::app()->name . ' - ' . Yum::t('Profile');
$this->breadcrumbs=array(Yum::t('Profile'));
$this->title = Yum::t('Profile');
?>

<div class="avatar">
<?php echo $model->renderAvatar(); ?>
	</div>
<table class="dataGrid">
<?php if(Yii::app()->getModule('user')->loginType != 'LOGIN_BY_EMAIL') {?>
<tr>
<th class="label"><?php echo CHtml::encode($model->username); ?>
</th>
<td><?php echo CHtml::encode($model->username); ?>
</td>
</tr>
<?php 
}
$profileFields = YumProfileField::model()->forOwner()->sort()->with('group')->together()->findAll();
if ($profileFields) {
	foreach($profileFields as $field) {
		if($field->field_type == 'DROPDOWNLIST') {
			?>
			<tr>
				<th class="label"><?php echo CHtml::encode(Yum::t($field->title)); ?>
				</th>
				<td><?php echo CHtml::encode($model->profile[0]->{ucfirst($field->varname)}->{$field->related_field_name}); ?>
				</td>
				</tr>
				<?php
		} else {
			?>
				<tr>
				<th class="label"><?php echo CHtml::encode(Yum::t($field->title)); ?>
				</th>
				<td><?php echo CHtml::encode($model->profile[0]->getAttribute($field->varname)); ?>
				</td>
				</tr>
				<?php
		}
	}
}
?>
<tr>
<th class="label"><?php echo Yum::t('first visit'); ?>
</th>
<td><?php echo date(UserModule::$dateFormat,$model->createtime); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo Yum::t('last visit'); ?>
</th>
    <td><?php echo date(UserModule::$dateFormat,$model->lastvisit); ?>
</td>
</tr>
</table>

<?php
if(Yii::app()->getModule('user')->messageSystem != YumMessage::MSG_NONE) {
	echo CHtml::link('Write a Message to this User', array(
				'messages/compose', 'to_user_id' => $model->id));
}
?>
