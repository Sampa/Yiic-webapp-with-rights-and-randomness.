<?php
$this->breadcrumbs=array(
	Yii::t("UserModule.user", 'Roles')=>array('index'),
	Yii::t("UserModule.user", 'View'),
);
?>

<?php
$this->menu = array(
	YumMenuItemHelper::manageRoles(),
	YumMenuItemHelper::manageUsers(),
	YumMenuItemHelper::createRole(),
	YumMenuItemHelper::updateRole(array('id'=>$model->id))
);
?>


<h2> <?php echo $model->title; ?> </h2>
<?php echo $model->description; ?>

<hr />

<p> 
<?php 
echo Yii::t('UserModule.user',
		'This users have been assigned to this Role'); ?> 
</p>

	<?php 
if($model->users) 
{
	foreach($model->users as $user) {
		printf("<li>%s</li>", CHtml::link($user->username, array(YumHelper::route('{users}/view'), 'id' => $user->id)));

	}
}
else 
{
	echo '<p> None </p>';
}

?>
