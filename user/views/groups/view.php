<?php
$this->breadcrumbs=array(
'Usergroups'=>array('index'),
	$model->title,
	);

$this->menu=array(
		array('label'=>Yii::t('app', 'List') . ' Usergroup', 'url'=>array('index')),
		array('label'=>Yii::t('app', 'Create') . ' Usergroup', 'url'=>array('create')),
		array('label'=>Yii::t('app', 'Update') . ' Usergroup', 'url'=>array('update', 'id'=>$model->id)),
		array('label'=>Yii::t('app', 'Delete') . ' Usergroup', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
		array('label'=>Yii::t('app', 'Manage') . ' Usergroup', 'url'=>array('admin')),
		);
?>

<h1><?php echo Yii::t('app', 'View');?> Usergroup #<?php echo $model->id; ?></h1>

<?php
$locale = CLocale::getInstance(Yii::app()->language);

 $this->widget('zii.widgets.CDetailView', array(
'data'=>$model,
	'attributes'=>array(
					'id',
		'owner_id',
		'title',
		'description',
),
	)); ?>


	