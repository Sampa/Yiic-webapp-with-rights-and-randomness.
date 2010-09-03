<?php 
$this->breadcrumbs = array(Yum::t('Data Generation'));
echo CHtml::beginForm();

printf('Generate %s %s users belonging to role %s',
		CHtml::textField('user_amount', '1', array('size' => 2)),
		CHtml::dropDownList('status', 1, array(
				'-1' => Yum::t('banned'),
				'0' => Yum::t('inactive'),
				'1' => Yum::t('active'))),
		CHtml::dropDownList('role', '', CHtml::listData(
				YumRole::model()->findAll(), 'id', 'title')));

echo '<br />';
echo CHtml::submitButton(Yum::t('Generate'));
echo CHtml::endForm();
?>
