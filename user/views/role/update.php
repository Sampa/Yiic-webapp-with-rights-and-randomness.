<?php
$this->title = Yii::t("UserModule.user", "Update role");

$this->breadcrumbs=array(
	Yii::t("UserModule.user", 'Roles')=>array('index'),
	Yii::t("UserModule.user", 'Update'));

?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
