<?php
$this->title = Yii::t('UserModule.user', 'Change setting profile');
$this->breadcrumbs=array(
	Yii::t('UserModule.user','User administration panel')=>array('//user/user/adminpanel'),
	Yii::t('UserModule.user', 'Module settings')=>array('index'),
	$model->title=>array('update','id'=>$model->id),
	Yii::t('UserModule.user', 'Update'),
);

$this->menu=array(
	array('label'=>Yii::t('UserModule.user', 'Create new setting profile'), 'url'=>array('create')),
	array('label'=>Yii::t('UserModule.user', 'View setting profile'), 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>Yii::t('UserModule.user', 'Manage settings profiles'), 'url'=>array('admin')),
);
?>

<h2> <?php echo $model->title; ?> </h2>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'yum-settings-form',
	'enableAjaxValidation'=>true,
)); 
echo $this->renderPartial('_form', array(
	'model'=>$model,
	'form' =>$form
	)); ?>

<div class="row buttons">
	<?php
	$url = array(Yii::app()->request->getQuery('returnTo'));
	if(empty($url[0])) 
		$url = array('yumsettings/admin');
echo CHtml::Button(Yii::t('UserModule.user', 'Cancel'), array('submit' => $url)); ?>&nbsp;

<?php echo CHtml::submitButton(Yii::t('UserModule.user', 'Update')); ?>
</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
