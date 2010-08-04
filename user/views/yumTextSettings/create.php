<?php
$this->breadcrumbs=array(
	'Yum Text Settings'=>array(Yii::t('app', 'index')),
	Yii::t('app', 'Create'),
);

$this->menu=array(
	array('label'=>Yii::t('app', 'List') . ' YumTextSettings', 'url'=>array('index')),
	array('label'=>Yii::t('app', 'Manage') . ' YumTextSettings', 'url'=>array('admin')),
);
?>

<h1> Create YumTextSettings </h1>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'yum-text-settings-form',
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
		$url = array('yumtextsettings/admin');
echo CHtml::Button(Yii::t('app', 'Cancel'), array('submit' => $url)); ?>&nbsp;
<?php echo CHtml::submitButton(Yii::t('app', 'Create')); ?>
</div>

<?php $this->endWidget(); ?>

</div>
