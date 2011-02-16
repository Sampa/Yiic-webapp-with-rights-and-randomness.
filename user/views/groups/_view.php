<div class="view">

<h3> <?php echo CHtml::encode($data->title); ?> </h3> 
	<b><?php echo CHtml::encode($data->getAttributeLabel('owner_id')); ?>:</b>
<?php if(isset($data->owner))
	echo CHtml::encode($data->owner->username); ?>
	<br />


	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode(substr($data->description, 0, 200)) . '... '; ?>
	<br />
	<br />
	<br />

	<?php echo CHtml::link(Yum::t('View Details'), array(
					'//user/groups/view', 'id' => $data->id)); ?>
	<?php 
if(!(Yii::app()->user->data()->belongsToGroup($data->id)))
	echo CHtml::link(Yum::t('Join group'), array(
					'//user/groups/join', 'id' => $data->id)); ?>

	</div>
