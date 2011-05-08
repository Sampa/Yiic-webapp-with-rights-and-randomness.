<?php
Yum::register('css/yum.css');

$this->breadcrumbs=array(
		Yum::t('Usergroups')=>array('index'),
		$model->title,
		);

$this->title = $model->title; ?> 

<?php
$this->widget('zii.widgets.CDetailView', array(
			'data'=>$model,
			'attributes'=>array(
				'owner.username',
				'title',
				'description',
				),
			)); 

printf('<h2> %s </h2>', Yum::t('Participants'));

$this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$participants,
    'itemView'=>'_participant', 
));

?>

<div style="clear: both;"> </div>



