<?php
$this->title = Yii::t('UserModule.user','View role {role}', array(
			'{role}' => $model->title));

$this->breadcrumbs=array(
	Yii::t("UserModule.user", 'Roles')=>array('index'),
	Yii::t("UserModule.user", 'View'));

echo $model->description; ?>

<p> 
<?php 
echo Yii::t('UserModule.user',
		'This users have been assigned to this role'); ?> 
</p>

	<?php 
if($model->users) {
	foreach($model->users as $user) {
		printf("<li>%s</li>", CHtml::link($user->username, array(Yum::route('user/view'), 'id' => $user->id)));

	}
} else {
	printf('<p> %s </p>', Yii::t('UserModule.user', 'None'));
}

if(Yii::app()->user->isAdmin())
	echo CHtml::Button(Yum::t('Update Role'), array('submit' => array('role/update', 'id' => $model->id)));

?>
