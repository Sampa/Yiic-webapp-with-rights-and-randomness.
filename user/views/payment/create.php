<?php
$this->breadcrumbs=array(
	'Payments'=>array(Yii::t('app', 'index')),
	Yii::t('app', 'Create'),
);

?>

<h1> <?php echo Yum::t('Create new payment type'); ?> </h1>
<?php
$this->renderPartial('_form', array(
			'model' => $model,
			'buttons' => 'create'));

?>

