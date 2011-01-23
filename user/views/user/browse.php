<?php
$this->title = Yum::t('Benutzer suchen');
$this->breadcrumbs=array(Yum::t('Benutzer suchen'));

Yum::register('js/tooltip.min.js');
Yum::register('css/yum.css'); 
?>
<h2>Benutzer suchen</h2>
<div class="search_options">

<?php echo CHtml::beginForm(); ?>

<div style="float: left;">
<?php
echo CHtml::label(Yum::t('Search for username'), 'search_username') . '<br />';
echo CHtml::textField('search_username',
		$search_username, array(
			'submit' => array('//user/user/browse')));
echo CHtml::submitButton(Yum::t('Search'));
?>

</div>

<?php
echo CHtml::label(Yum::t('Having'), 'search_role') . '<br />';
echo CHtml::dropDownList('search_role', isset($role) ? $role : '',
		CHtml::listData(YumRole::model()->searchable()->findAll(), 'id', 'description'), array(
			'submit' => array('//user/user/browse'),
			'empty' => ' - ',
			));
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

</div>

<div style="clear: both;"> </div>


