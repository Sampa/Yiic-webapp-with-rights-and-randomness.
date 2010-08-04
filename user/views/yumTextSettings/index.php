<?php
$this->breadcrumbs = array(
	'Yum Text Settings',
	Yii::t('app', 'Index'),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'Create') . ' YumTextSettings', 'url'=>array('create')),
	array('label'=>Yii::t('app', 'Manage') . ' YumTextSettings', 'url'=>array('admin')),
);
?>

<h1>Yum Text Settings</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
