<?php
$this->title = $model->title;

$this->breadcrumbs=array('Messages'=>array('index'),$model->title);

?>

<h3> <?php echo Yii::t('UserModule.user', 'Message from ') . 
'<em>' . $model->from_user->username . '</em>';

echo ': ' . $model->title; ?> 
</h3>

<?php echo $model->message; ?>

