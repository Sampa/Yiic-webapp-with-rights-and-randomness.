<?php
$this->title = Yii::t('UserModule.user', 'User Management settings configuration'); 
$this->breadcrumbs=array(
	Yii::t('UserModule.user','User administration panel')=>array('//user/user/adminpanel'),
	Yii::t('UserModule.user', 'Manage'),
);

$this->menu=array(
		array('label'=>Yii::t('UserModule.user', 'Create new settings profile'),
			'url'=>array('/settings/create')),
		);
		?>

		<?php $this->renderPartial('/settings/choose_active_profile', array(
					'returnTo' => '/settings/index',
					'model' => $model)); ?>
		<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'yum-settings-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'title',
		array(
			'name'=>'is_active',
			'value'=>'$data->is_active?Yii::t("UserModule.user","Yes"):Yii::t("UserModule.user","No")',
			'filter' => false,
			),
		array(
			'class'=>'CButtonColumn',
			'template' => '{update}{delete}',
		),
	),
)); ?>
