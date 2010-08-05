<?php
$this->title = Yii::t('UserModule.user', 'Manage users');

$this->breadcrumbs = array(
	Yii::t("UserModule.user", 'Users') => array('index'),
	Yii::t("UserModule.user", 'Manage'));

if(Yii::app()->user->hasFlash('adminMessage')) 
	printf('<div class="errorSummary">%s</div>', Yii::app()->user->getFlash('adminMessage')); 

$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$model->search(),
	'filter' => $model,
		'columns'=>array(
			array(
				'name'=>'id',
				'type'=>'raw',
				'value'=>'CHtml::link(CHtml::encode($data->id),
				array(Yum::route("{user}/update"),"id"=>$data->id))',
			),
			array(
				'name'=>'username',
				'type'=>'raw',
				'value'=>'CHtml::link(CHtml::encode($data->username),
				array(Yum::route("{user}/view"),"id"=>$data->id))',
			),
			array(
				'name'=>'createtime',
				'value'=>'date(UserModule::$dateFormat,$data->createtime)',
			),
			array(
				'name'=>'lastvisit',
				'value'=>'date(UserModule::$dateFormat,$data->lastvisit)',
			),
			array(
				'name'=>'status',
				'value'=>'YumUser::itemAlias("UserStatus",$data->status)',
			),
			array(
				'name'=>'superuser',
				'value'=>'YumUser::itemAlias("AdminStatus",$data->superuser)',
			),
			array(
				'class'=>'CButtonColumn',
			),
))); ?>
