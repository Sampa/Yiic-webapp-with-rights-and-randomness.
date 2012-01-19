<?php
if(!$profile = $model->profile)
	$profile = new YumProfile;


$this->pageTitle = Yii::app()->name . ' - ' . Yum::t('Profile');
$this->title = CHtml::activeLabel($model,'username');
$this->breadcrumbs = array(Yum::t('Profile'), $model->username);
Yum::renderFlash();
?>

<div id="profile">

<?php
if($model->id == Yii::app()->user->id
		&& Yum::hasModule('message')) {
	Yii::import('application.modules.message.models.YumMessage');
	$this->renderPartial(
			'application.modules.message.views.message.new_messages');
}
?>

<?php echo $model->getAvatar(); ?>
<?php $this->renderPartial(Yum::module('profile')->publicFieldsView, array(
			'profile' => $model->profile)); ?>
<br />
<?php
if(Yum::hasModule('friendship'))
$this->renderPartial(
		'application.modules.friendship.views.friendship.friends', array(
			'model' => $model)); ?>
<br />
<?php
if(@Yum::module('message')->messageSystem != 0)
$this->renderPartial('/message/write_a_message', array(
			'model' => $model)); ?>
<br />
<?php
if(Yum::module('profile')->enableProfileComments
		&& Yii::app()->controller->action->id != 'update'
		&& isset($model->profile))
	$this->renderPartial(Yum::module('profile')->profileCommentIndexView, array(
			 'model' => $model->profile)); ?>
 </div>
