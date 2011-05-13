<?php
$this->title = Yum::t('Benutzer suchen');
$this->breadcrumbs=array(Yum::t('Benutzer suchen'));

Yum::register('js/tooltip.min.js');
Yum::register('css/yum.css'); 
?>
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


