<?php
$this->breadcrumbs=array(
	'Payments'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	Yii::t('app', 'Update'),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'List') . ' Payment', 'url'=>array('index')),
	array('label'=>Yii::t('app', 'Create') . ' Payment', 'url'=>array('create')),
	array('label'=>Yii::t('app', 'View') . ' Payment', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>Yii::t('app', 'Manage') . ' Payment', 'url'=>array('admin')),
);
?>

<h1> <?php echo Yii::t('app', 'Update');?> Payment #<?php echo $model->id; ?> </h1>
<?php
$this->renderPartial('_form', array(
			'model'=>$model));
?>
