<?php
$this->pageTitle=Yii::app()->name . ' - ' . Yii::t("UserModule.user", "Profile");
$this->breadcrumbs=array(Yii::t("UserModule.user", "Profile"));
$this->title = Yii::t("UserModule.user", 'Your profile');
?>

<?php
$this->renderPartial('/messages/new_messages');?>

<?php if(Yii::app()->user->hasFlash('profileMessage')): ?>
<div class="errorSummary">
<?php echo Yii::app()->user->getFlash('profileMessage'); ?>
</div>
<?php endif; ?>
<div class="avatar">
<?php echo $model->renderAvatar(); ?>
</div>

<table class="dataGrid">
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('username')); ?>
</th>
    <td><?php echo CHtml::encode($model->username); ?>
</td>
</tr>
<?php 
		$profileFields = YumProfileField::model()->forOwner()->sort()->with('group')->together()->findAll();
		if ($profileFields && Yii::app()->getModule('user')->enableProfiles) {
			foreach($profileFields as $field) {
			?>
<tr>
	<th class="label"><?php echo CHtml::encode(Yum::t($field->title)); ?>
</th>
    <td><?php echo CHtml::encode($profile[0]->getAttribute($field->varname)); ?>
</td>
</tr>
			<?php
			}
		}
?>
<tr>
	<th class="label"><?php echo CHtml::encode($model->getAttributeLabel('password')); ?>
</th>
    <td><?php echo CHtml::link(Yii::t("UserModule.user", "Change password"),array(Yum::route('{user}/changepassword'))); ?>
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
    <td><?php echo CHtml::encode(YumUser::itemAlias("UserStatus",$model->status));
    ?>
</td>
</tr>
</table>

<h2> <?php echo Yum::t('This users have visited my profile'); ?> </h2>
<?php
	if($model->visits) {
		$format = Yii::app()->getModule('user')->dateTimeFormat;
		echo '<table>';
		printf('<th>%s</th><th>%s</th><th>%s</th><th>%s</th><th>%s</th>',
			Yum::t('Visitor'),
			Yum::t('Time of first Visit'),
			Yum::t('Time of last Visit'),
			Yum::t('Num of Visits'),
			Yum::t('Message')
);

		foreach($model->visits as $visit) {
			printf('<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>',
					CHtml::link($visit->visitor->username, array('user/view', 'id' => $visit->visitor_id)),
					date($format, $visit->timestamp_first_visit),
					date($format, $visit->timestamp_last_visit),
					$visit->num_of_visits,
					CHtml::link(Yum::t('Write a message'), array('messages/compose', 'to_user_id' => $visit->visitor_id))
					);
		}
		echo '<table>';
	} else {
		echo Yum::t('Nobody has visited your profile yet');
	}
?>
