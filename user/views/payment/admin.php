<?php
$this->breadcrumbs=array(
	'Payments'=>array(Yii::t('app', 'index')),
	Yii::t('app', 'Manage'),
);
		?>

<h1> <?php echo Yii::t('app', 'Manage'); ?> Payments</h1>

<?php
$locale = CLocale::getInstance(Yii::app()->language);

 $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'payment-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'title',
		'text',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
