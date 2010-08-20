<?php
$this->breadcrumbs=array(
	'Yum Text Settings'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>Yii::t('app', 'List') . ' YumTextSettings', 'url'=>array('index')),
	array('label'=>Yii::t('app', 'Create') . ' YumTextSettings', 'url'=>array('create')),
	array('label'=>Yii::t('app', 'Update') . ' YumTextSettings', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>Yii::t('app', 'Delete') . ' YumTextSettings', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>Yii::t('app', 'Manage') . ' YumTextSettings', 'url'=>array('admin')),
);
?>

<h1>View YumTextSettings #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'language',
		'text_registration_header',
		'text_registration_footer',
		'text_login_header',
		'text_login_footer',
		'text_email_registration',
		'text_email_recovery',
		'text_email_activation',
	),
)); ?>


