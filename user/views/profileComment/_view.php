
<div style="float: left; margin: 10px;">
	<?php echo $data->user->getAvatar(null, true); ?>
</div>

	<b><?php echo CHtml::encode($data->getAttributeLabel('user_id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->user->username), array('//user/user/view', 'id' => $data->user_id)); ?>
	<br />
	<b><?php echo CHtml::encode($data->getAttributeLabel('createtime')); ?>:</b>
<?php $locale = CLocale::getInstance(Yii::app()->language);
	echo $locale->getDateFormatter()->formatDateTime($data->createtime, 'medium', 'medium'); ?>
	<br />

	<?php echo CHtml::encode($data->comment); ?>

	<?php if($data->user_id == Yii::app()->user->id
			|| $data->profile_id == Yii::app()->user->id ) {
		echo CHtml::Button(Yum::t('Remove comment'), array(
					'confirm' => Yum::t('Are you sure to remove this comment from your profile?'),
					'submit' => array( '//user/comments/delete', 'id' => $data->id)));
	}
?>

<div style="clear: both;"> </div>
