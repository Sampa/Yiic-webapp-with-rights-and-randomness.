<?php
$this->title = Yii::t('UserModule.user','{role}', array(
			'{role}' => $model->title));

$this->breadcrumbs=array(
	Yum::t('Roles')=>array('index'),
	Yum::t('View'),
	$model->title
);

printf('<h2>%s</h2>', $model->title);
echo '<br />';
echo $model->description; ?>

<br />
<p> <?php echo Yum::t('This users have been assigned to this role'); ?> </p>

<?php 
if($model->users) {
	foreach($model->users as $user) {
		printf("<li>%s</li>", CHtml::link($user->username, array(Yum::route('user/view'), 'id' => $user->id)));

	}
} else 
printf('<p> %s </p>', Yum::t('None'));

if(Yii::app()->user->isAdmin())
	echo CHtml::Button(Yum::t('Update role'), array('submit' => array('role/update', 'id' => $model->id)));

?>
