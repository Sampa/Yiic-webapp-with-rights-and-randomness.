<?php
$this->title = Yum::t('My inbox');

$this->breadcrumbs=array(
	Yum::t('Messages')=>array('index'),
	Yum::t('My inbox'));

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'yum-messages-grid',
	'dataProvider' => $dataProvider,
	'columns'=>array(
		array(
			'type' => 'raw',
			'name' => Yum::t('from'),
			'value' => 'CHtml::link($data->from_user->username, array(
					Yum::route(\'user/profile\'),
					"id" => $data->from_user_id)
				)'
			),
		array(
			'type' => 'raw',
			'name' => Yii::t('UserModule.user', 'title'),
			'value' => '$data->getTitle()',
		),
		array(
			'class'=>'CButtonColumn',
			'template' => '{view}{delete}',
		),
	),
)); ?>
