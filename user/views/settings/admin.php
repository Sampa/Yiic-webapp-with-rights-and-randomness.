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
			'filter'=>array('0'=>Yii::t("UserModule.user","No"),'1'=>Yii::t("UserModule.user","Yes")),
			),

		array(
			'name'=>'preserveProfiles',
			'value'=>'$data->preserveProfiles?Yii::t("UserModule.user","Yes"):Yii::t("UserModule.user","No")',
			'filter'=>array('0'=>Yii::t("UserModule.user","No"),'1'=>Yii::t("UserModule.user","Yes")),
			),
		array(
			'name'=>'enableRegistration',
			'value'=>'$data->enableRegistration?Yii::t("UserModule.user","Yes"):Yii::t("UserModule.user","No")',
			'filter'=>array('0'=>Yii::t("UserModule.user","No"),'1'=>Yii::t("UserModule.user","Yes")),
			),
		array(
			'name'=>'enableRecovery',
			'value'=>'$data->enableRecovery?Yii::t("UserModule.user","Yes"):Yii::t("UserModule.user","No")',
			'filter'=>array('0'=>Yii::t("UserModule.user","No"),'1'=>Yii::t("UserModule.user","Yes")),
			),
		array(
			'name'=>'enableEmailActivation',
			'value'=>'$data->enableEmailActivation?Yii::t("UserModule.user","Yes"):Yii::t("UserModule.user","No")',
			'filter'=>array('0'=>Yii::t("UserModule.user","No"),'1'=>Yii::t("UserModule.user","Yes")),
			),
		array(
			'name'=>'enableProfileHistory',
			'value'=>'$data->enableProfileHistory?Yii::t("UserModule.user","Yes"):Yii::t("UserModule.user","No")',
			'filter'=>array('0'=>Yii::t("UserModule.user","No"),'1'=>Yii::t("UserModule.user","Yes")),
			),
		array(
				'name'=>'readOnlyProfiles',
				'value'=>'$data->readOnlyProfiles?Yii::t("UserModule.user","Yes"):Yii::t("UserModule.user","No")',
				'filter'=>array('0'=>Yii::t("UserModule.user","No"),'1'=>Yii::t("UserModule.user","Yes")),
				),
		array(
				'name'=>'messageSystem',
				'value'=>'$data->messageSystem',
				'filter'=>array('None'=>Yii::t("UserModule.user","None"),'Plain'=>Yii::t("UserModule.user","Plain")),
				),
		array(
				'name'=>'enableCaptcha',
				'value'=>'$data->enableCaptcha?Yii::t("UserModule.user","Yes"):Yii::t("UserModule.user","No")',
				'filter'=>array('0'=>Yii::t("UserModule.user","No"),'1'=>Yii::t("UserModule.user","Yes")),
				),
/*		array(
				'name'=>'loginType',
				'value'=>'$data->itemAlias("loginType", $data->loginType)',
				'filter'=>array('0'=>Yii::t("UserModule.user","No"),'1'=>Yii::t("UserModule.user","Yes")),
				), */
		array(
			'class'=>'CButtonColumn',
			'template' => '{update}{delete}',
		),
	),
)); ?>
