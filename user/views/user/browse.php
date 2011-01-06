<?php
$this->title = Yum::t('Browse users');
$this->breadcrumbs=array(Yum::t('Browse'));

echo CHtml::beginForm();
echo CHtml::label(Yum::t('Search for username'), 'search_username') . '<br />';
echo CHtml::textField('search_username',
		$search_username, array(
			'submit' => array('//user/user/browse')));
		echo CHtml::endForm();

$this->widget('zii.widgets.CListView', array(
    'dataProvider'=>$dataProvider,
    'itemView'=>'_view', 
    'sortableAttributes'=>array(
        'username',
        'lastvisit',
    ),
));

?>


