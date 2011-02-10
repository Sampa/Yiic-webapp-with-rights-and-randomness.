<?php 
if(!$profile = @$model->profile)
	return false;

$this->pageTitle = Yii::app()->name . ' - ' . Yum::t('Profile');
$this->title = CHtml::activeLabel($model,'username'); 
$this->breadcrumbs = array(Yum::t('Profile'), $model->username);
Yum::renderFlash(); 
?>

<div id="profile">

<?php 
if($model->id == Yii::app()->user->id)
	$this->renderPartial('/messages/new_messages');
?>

<?php echo $model->getAvatar(); ?>"
<?php $this->renderPartial('/profile/public_fields', array(
			'profile' => $model->profile)); ?>
<br />
<?php $this->renderPartial('/friendship/friends', array('model' => $model)); ?> 
<br /> 
<?php $this->renderPartial('/messages/write_a_message', array('model' => $model)); ?> 
<br /> 
<?php
 if(isset($model->profile))
	$this->renderPartial('/profileComment/index', array('model' => $model->profile)); ?> 
</div>
