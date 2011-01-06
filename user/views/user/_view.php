<div class="view">
<?php echo $data->getAvatar(null, true); ?>
<?php printf('<h3>%s</h3>', $data->username); ?>

<?php echo CHtml::link(Yum::t('Jump to profile'), array(
				'//user/profile/view', 'id' => $data->id)); ?>
</div>
