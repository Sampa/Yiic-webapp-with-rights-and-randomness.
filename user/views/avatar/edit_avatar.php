<?php
$this->title = Yum::t('Upload avatar');

$this->breadcrumbs = array('Avatar upload');

if($model->avatar) {
	echo Yum::t('Your Avatar image');
	echo '<br />';
	$model->renderAvatar();
}
else
	echo Yum::t('You do not have set an avatar image yet.');

	echo '<br />';

	echo CHtml::errorSummary($model);
	echo CHtml::beginForm(array('//user/avatar/editAvatar'), 'POST', array(
				'enctype' => 'multipart/form-data'));
	echo '<div class="row">';
	echo CHtml::activeLabelEx($model, 'avatar');
	echo CHtml::activeFileField($model, 'avatar');
	echo CHtml::error($model, 'avatar');
	echo '</div>';
	echo CHtml::Button('Remove Avatar', array(
				'submit' => array(
					'avatar/removeAvatar')));
	echo CHtml::submitButton('Upload Avatar');
	echo CHtml::endForm();

