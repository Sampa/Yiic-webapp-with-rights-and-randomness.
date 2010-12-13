
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

<div style="clear: both;"> </div>
