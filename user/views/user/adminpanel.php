<?php
// Yii User Management Administration Panel
$this->title = Yii::t('UserModule.user', 'User Administration Panel');

// Breadcrumbs
$this->breadcrumbs = array(
	Yii::t("UserModule.user", 'Users') => array('index'),
	Yii::t("UserModule.user", 'Administration panel'));
?>

<div id="users">
	<ul>
	<?php printf('<li>%s</li>', CHtml::link(Yii::t('UserModule.user', 'Manage Users'), array('/user/user/admin'))); ?>
	</ul>
</div>
<div id="roles">
	<?php printf('<li>%s</li>', CHtml::link(Yii::t('UserModule.user', 'Manage Roles'), array('/user/role/admin'))); ?>
</div>
<div id="profiles">
	<?php printf('<li>%s</li>', CHtml::link(Yii::t('UserModule.user', 'Manage Profiles'), array('/user/profile/admin'))); ?>
	<?php printf('<li>%s</li>', CHtml::link(Yii::t('UserModule.user', 'Manage Profile fields'), array('/user/profile/admin'))); ?>
</div>
</div>
