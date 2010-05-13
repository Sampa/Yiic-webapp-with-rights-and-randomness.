<?php
$this->breadcrumbs=array(
	Yii::t("UserModule.user", 'Profile Fields Groups')=>array('admin'),
	Yii::t("UserModule.user", 'Manage'),
);

$this->menu = array(
	YumMenuItemHelper::manageUsers(),
	YumMenuItemHelper::manageFields(),
	YumMenuItemHelper::createFieldsGroup(),
);

Yii::app()->clientScript->registerScript('search', "
$('.search-button').click(function(){
	$('.search-form').toggle();
	return false;
});
$('.search-form form').submit(function(){
	$.fn.yiiGridView.update('yum-profile-fields-group-grid', {
		data: $(this).serialize()
	});
	return false;
});
");
?>

<h1><?php echo Yii::t("UserModule.user", 'Manage profile fields groups'); ?></h1>

<?php echo CHtml::link(Yii::t("UserModule.user",'Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div><!-- search-form -->

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'yum-profile-fields-group-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'group_name',
		array(
			'name'=>'title',
			'value'=>'Yii::t("UserModule.user", $data->title)',
		),
		'position',
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
