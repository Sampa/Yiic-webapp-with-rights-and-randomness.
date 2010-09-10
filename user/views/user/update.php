<?php

if(empty($tabularIdx))
{
	$this->title=Yii::t("UserModule.user", 'Update user')." ".$model->id;

	$this->breadcrumbs = array(
			Yii::t("UserModule.user", 'Users')=>array('index'),
			$model->username=>array('view','id'=>$model->id),
			Yii::t("UserModule.user", 'Update'));
}

echo $this->renderPartial('/user/_form', array(
			'model'=>$model,
			'passwordform'=>$passwordform,
			'changepassword' => isset($changepassword) ? $changepassword : false,
			'profile'=>$profile,
			'tabularIdx'=> isset($tabularIdx) ? $tabularIdx : 0)
		);
?>
