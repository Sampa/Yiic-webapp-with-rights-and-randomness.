<div id="profile">
<h2> <?php echo CHtml::activeLabel($model,'username'); ?> </h2>
<?php
$this->pageTitle = Yii::app()->name . ' - ' . Yum::t('Profile');
$this->breadcrumbs = array(Yum::t('Profile'), $model->username);
$this->title = Yum::t('Profile');
Yum::renderFlash(); 
?>


<?php 
if($model->id == Yii::app()->user->id)
	$this->renderPartial('/messages/new_messages');

?>

<?php echo $model->getAvatar(); ?>
<?php $this->renderPartial('public_fields', array('profile' => $model->profile[0])); ?>
<br />
<?php $this->renderPartial('/friendship/friends', array('model' => $model)); ?> 
<br /> 
<?php $this->renderPartial('/messages/write_a_message', array('model' => $model)); ?> 
<br /> 
<?php
 if(isset($model->profile[0]))
	$this->renderPartial('/profileComment/index', array('model' => $model->profile[0])); ?> 
</div>
