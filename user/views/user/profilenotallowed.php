<?php

$this->menu = array(
		array(
			'label'=> Yii::t('UserModule.user', 'Back to Profile'),
			'url'=>array('profile')
			)
		);

?>

<div class="hint">
	<p> <?php echo Yii::t('UserModule.user',
	  'You are not allowed to view this profile.'); ?> </p>
</div>
