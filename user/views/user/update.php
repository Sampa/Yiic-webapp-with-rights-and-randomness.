<?php

if(empty($tabularIdx))
{
	$this->title=Yii::t("UserModule.user", 'Update user')." ".$model->id;

	$this->breadcrumbs = array(
			Yii::t("UserModule.user", 'Users')=>array('index'),
			$model->username=>array('view','id'=>$model->id),
			Yii::t("UserModule.user", 'Update'));
}

echo $this->renderPartial('_form', array(
			'model'=>$model,
			'passwordform'=>$passwordform,
			'changepassword' => $changepassword,
			'profile'=>$profile,
			'tabularIdx'=>$tabularIdx)
		);
?>
