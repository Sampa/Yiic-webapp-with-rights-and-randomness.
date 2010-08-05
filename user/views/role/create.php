<?php
$this->title = Yii::t("UserModule.user", "Create role");

$this->breadcrumbs=array(
	Yii::t("UserModule.user", 'Roles')=>array('index'),
	Yii::t("UserModule.user", 'Create'));

?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
