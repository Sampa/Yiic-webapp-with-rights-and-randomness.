<?php

if(empty($tabularIdx))
{
// don' t display when used in multiform
	echo CHtml::openTag('div',array('class'=>'form'));
	echo CHtml::beginForm();
	echo YumHelper::requiredFieldNote();
}

echo CHtml::errorSummary(array($model, $profile, $passwordform));

$attribute = !empty($tabularIdx) ? "[{$tabularIdx}]username" : "username";

echo CHtml::openTag('div',array('class'=>'row'));
echo CHtml::activeLabelEx($model,$attribute);
echo CHtml::activeTextField($model,$attribute,array('size'=>20,'maxlength'=>20));
echo CHtml::error($model,$attribute);
echo CHtml::closeTag('div');

$attribute = !empty($tabularIdx) ? "[{$tabularIdx}]password" : "password";
if(!$model->isNewRecord) {
	$model->password = '';
	$function = "$('#password').toggle()";
	echo CHtml::label(Yii::t('UserModule.user', 'change Password'), 'change_password');
	echo CHtml::checkBox(Yii::t('UserModule.user', 'change Password'),
			$changepassword,
			array('onClick' => $function));
}
echo CHtml::openTag('div',array(
			'style' => $changepassword ? '' : 'display: none;',
			'id' => 'password',
			'class'=>'row'));
$this->renderPartial('passwordfields', array('form'=>$passwordform));
echo CHtml::closeTag('div');

foreach($profile->loadProfileFields() as $field)
{
	echo CHtml::openTag('div',array('class'=>'row'));
	$attribute = !empty($tabularIdx) ? "[{$tabularIdx}]{$field->varname}" : $field->varname;
	echo CHtml::activeLabelEx($profile, $attribute);
	if ($field->field_type=="TEXT")
		echo CHtml::activeTextArea($profile, $attribute, array(
					'rows'=>6,
					'cols'=>50)
				);
	else
		echo CHtml::activeTextField($profile, $attribute, array(
					'size'=>60,
					'maxlength'=>(($field->field_size)?$field->field_size:255)));
	echo CHtml::error($profile, $attribute);
	if($field->hint)
		echo CHtml::tag('div',array('class'=>'hint'),$field->hint,true);
	echo CHtml::closeTag('div');
} 

$attribute = !empty($tabularIdx) ? "[{$tabularIdx}]superuser" : "superuser";
echo CHtml::openTag('div',array('class'=>'row'));
echo CHtml::activeLabelEx($model,$attribute);
echo CHtml::activeDropDownList($model,$attribute,YumUser::itemAlias('AdminStatus'));
echo CHtml::error($model,$attribute);
echo CHtml::closeTag('div');

$attribute = !empty($tabularIdx) ? "[{$tabularIdx}]status" : "status";
echo CHtml::openTag('div',array('class'=>'row'));
echo CHtml::activeLabelEx($model,$attribute);
echo CHtml::activeDropDownList($model,$attribute,YumUser::itemAlias('UserStatus'));
echo CHtml::error($model,$attribute);
echo CHtml::closeTag('div');



echo CHtml::openTag('div',array( 'class'=>'row'));
echo CHtml::tag('p',
		array(),
		Yii::t('UserModule.user', 'User belongs to these roles'),true);

$this->widget('YumModule.components.Relation',
		array('model' => $model,
			'relation' => 'roles',
			'style' => 'checkbox',
			'fields' => 'title',
			'htmlOptions' => array(
				'checkAll' => Yii::t('UserModule.user', 'Choose All'),
				'template' => '<div style="float:left;margin-right:5px;">{input}</div>{label}'),
				'showAddButton' => false
				));
	echo CHtml::closeTag('div');

		echo CHtml::openTag('div',array('class'=>'row'));
		echo CHtml::tag('p',array(),Yii::t('UserModule.user', 'This user can administer this users'),true);
		$this->widget('YumModule.components.Relation',
				array('model' => $model,
					'relation' => 'users',
					'style' => 'checkbox',
					'fields' => 'username',
					'htmlOptions' => array(
						'checkAll' => Yii::t('UserModule.user', 'Choose All'),
						'template' => '<div style="float:left;margin-right:5px;">{input}</div>{label}'),
						'showAddButton' => false
					));
		echo CHtml::closeTag('div');
if(empty($tabularIdx))
{
	echo CHtml::openTag('div',array('class'=>'row buttons'));
	echo CHtml::submitButton($model->isNewRecord
			? Yii::t('UserModule.user', 'Create')
			: Yii::t('UserModule.user', 'Save'));
	echo CHtml::closeTag('div');

	echo CHtml::endForm();
	echo CHtml::closeTag('div');
	echo '<div style="clear:both;"></div>';
}
?>
