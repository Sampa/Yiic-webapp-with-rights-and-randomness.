<?php
$this->breadcrumbs=array(
	'Permissions'=>array('index'),
	'Manage',
);

?>

<h1> Manage permissions </h1>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'action-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	)
); ?>
