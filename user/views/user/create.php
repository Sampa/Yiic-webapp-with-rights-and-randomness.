<?php
    if(empty($tabularIdx))
    {
    $this->title = Yii::t('UserModule.user', "Create user");
    $this->breadcrumbs = array(
        Yii::t('UserModule.user', 'Users') => array('index'),
    Yii::t('UserModule.user', 'Create'));

    echo $this->renderPartial('_form', array(
		'model'=>$model,
		'changepassword' => true,
		'passwordform'=>$passwordform,
		'profile'=>$profile,
		'tabularIdx'=>$tabularIdx));
	}
?>
