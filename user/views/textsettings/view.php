<?php
$this->title = Yum::t('Yum Text Settings');
$this->breadcrumbs=array(
	Yum::t('Yum Text Settings')=>array('index'),
	$model->language,
);

$this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'language',
		'text_registration_header',
		'text_registration_footer',
		'text_login_header',
		'text_login_footer',
		'text_email_registration',
		'text_email_recovery',
		'text_email_activation',
		'text_friendship_new',
		'text_profilecomment_new',
	),
)); ?>


