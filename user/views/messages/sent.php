<?php
$this->title = Yum::t('Send messages');

$this->breadcrumbs=array(
	Yum::t('Messages')=>array('index'),
	Yum::t('Send messages'));

$this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'yum-messages-grid',
	'dataProvider' => $dataProvider,
	'columns'=>array(
		array(
			'type' => 'raw',
			'name' => Yum::t('to'),
			'value' => 'CHtml::link($data->to_user->username, array(
					Yum::route(\'user/profile\'),
					"id" => $data->to_user_id)
				)'
			),
		array(
			'type' => 'raw',
			'name' => Yii::t('UserModule.user', 'Sent at'),
			'value' => '$data->getDate()',
		),

		array(
			'type' => 'raw',
			'name' => Yii::t('UserModule.user', 'title'),
			'value' => '$data->title',
		),
		array(
			'class'=>'CButtonColumn',
			'template' => '{view}{delete}',
		),
	),
)); ?>
