<?php
$this->title = $model->title;

$this->breadcrumbs=array('Messages'=>array('index'),$model->title);

?>

<h3> <?php echo Yii::t('UserModule.user', 'Message from ') . 
'<em>' . $model->from_user->username . '</em>';

echo ': ' . $model->title; ?> 
</h3>

<?php echo $model->message; ?>

<hr />
<?php
if(Yii::app()->user->id != $model->from_user_id)
 echo CHtml::Button(Yum::t('Reply to Message'), array(
			'submit' => array(
				'//user/messages/compose',
				'to_user_id' => $model->from_user_id)));
?>
