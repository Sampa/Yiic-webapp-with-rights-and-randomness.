<?php
$this->breadcrumbs = array(
	Yum::t('Usergroups'),
	Yum::t('Browse'),
);

?>

<h1> <?php echo Yum::t('Usergroups'); ?></h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
