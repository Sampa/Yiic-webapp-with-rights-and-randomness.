<?php
$this->breadcrumbs=array(
	'Yum Text Settings'=>array(Yii::t('app', 'index')),
	Yii::t('app', 'Manage'),
);

$this->menu=array(
		array('label'=>Yii::t('app', 'List') . ' YumTextSettings',
			'url'=>array('index')),
		array('label'=>Yii::t('app', 'Create') . ' YumTextSettings',
		'url'=>array('create')),
	);

		Yii::app()->clientScript->registerScript('search', "
			$('.search-button').click(function(){
				$('.search-form').toggle();
				return false;
				});
			$('.search-form form').submit(function(){
				$.fn.yiiGridView.update('yum-text-settings-grid', {
data: $(this).serialize()
});
				return false;
				});
			");
		?>

<h1> <?php echo Yii::t('app', 'Manage'); ?> Yum Text Settings</h1>

<?php echo CHtml::link(Yii::t('app', 'Advanced Search'),'#',array('class'=>'search-button')); ?>
<div class="search-form" style="display:none">
<?php $this->renderPartial('_search',array(
	'model'=>$model,
)); ?>
</div>

<?php $this->widget('zii.widgets.grid.CGridView', array(
	'id'=>'yum-text-settings-grid',
	'dataProvider'=>$model->search(),
	'filter'=>$model,
	'columns'=>array(
		'id',
		'language',
		'text_registration_header',
		'text_registration_footer',
		'text_login_header',
		'text_login_footer',
		/*
		'text_email_registration',
		'text_email_recovery',
		'text_email_activation',
		*/
		array(
			'class'=>'CButtonColumn',
		),
	),
)); ?>
