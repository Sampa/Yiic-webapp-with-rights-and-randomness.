<?php
$this->breadcrumbs = array(
	'Activities',
	Yii::t('app', 'Index'),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'Create') . ' Activities', 'url'=>array('create')),
	array('label'=>Yii::t('app', 'Manage') . ' Activities', 'url'=>array('admin')),
);
?>

<h1>Activities</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'sortableAttributes' => array('timestamp', 'user_id', 'action'),
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
