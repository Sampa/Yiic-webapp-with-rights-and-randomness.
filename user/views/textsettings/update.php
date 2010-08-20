<?php
$this->breadcrumbs=array(
	'Yum Text Settings'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	Yii::t('app', 'Update'),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'List') . ' YumTextSettings', 'url'=>array('index')),
	array('label'=>Yii::t('app', 'Create') . ' YumTextSettings', 'url'=>array('create')),
	array('label'=>Yii::t('app', 'View') . ' YumTextSettings', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>Yii::t('app', 'Manage') . ' YumTextSettings', 'url'=>array('admin')),
);
?>

<h1> <?php echo Yii::t('app', 'Update');?> YumTextSettings #<?php echo $model->id; ?> </h1>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'yum-text-settings-form',
	'enableAjaxValidation'=>true,
)); 
echo $this->renderPartial('/textsettings/_form', array(
	'model'=>$model,
	'form' =>$form
	)); ?>

<div class="row buttons">
	<?php
	$url = array(Yii::app()->request->getQuery('returnTo'));
	if(empty($url[0])) 
		$url = array('yumtextsettings/admin');
echo CHtml::Button(Yii::t('app', 'Cancel'), array('submit' => $url)); ?>&nbsp;

<?php echo CHtml::submitButton(Yii::t('app', 'Update')); ?>
</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
