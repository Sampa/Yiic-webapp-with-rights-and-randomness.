<?php
$this->pageTitle=Yii::app()->name . ' - ' . Yum::t('Profile');
$this->breadcrumbs=array(Yum::t('Profile'),$model->username);
$this->title = Yum::t('Profile');
?>
<div id="content">
<div class="avatar">
<?php echo $model->renderAvatar(); ?>
	</div>
<table class="dataGrid">
<?php if(Yii::app()->getModule('user')->loginType != 'LOGIN_BY_EMAIL') {?>
<tr>
<th class="label"><?php echo CHtml::activeLabel($model,'username'); ?>
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
if($model->profile[0]->show_friends == 2)
{
?>
<div id="friends">
<?php
if(isset($friends))
{
echo ucwords($model->username . '\'s friends');
foreach($friends as $friend)
{
	?>
<div id="friend">
<div id="avatar">
<?php
$model->renderAvatar($friend);
?>
<div id='username'>
<?php 
echo CHtml::link(ucwords($friend->username), Yii::app()->createUrl('user/profile/view',array('id'=>$friend->id)));
?>
</div>
</div>
</div>
<?php
//var_dump($friends);
}
}else{
	echo 'you have no friends.';
}
?>
</div>
<?php
}
if(Yum::module()->messageSystem != YumMessage::MSG_NONE) {
	echo CHtml::link('Write a Message to this User', array(
				'messages/compose', 'to_user_id' => $model->id));
}
	echo '<br />';
	echo YumFriendshipController::invitationLink(Yii::app()->user->id, $model->id);

?>
</div>
