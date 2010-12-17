<?php if($model->allow_comments) { ?>
<h1> <?php echo Yum::t('Profile Comments'); ?> </h1>

<?php 
$dataProvider = new CActiveDataProvider('YumProfileComment', array(
			'criteria'=>array(
				'condition'=>'profile_id = :profile_id',
				'params' => array(':profile_id' => $model->profile_id),
				'order'=>'createtime DESC')
			)
		);

echo CHtml::link(Yum::t('Write a comment'), '#', array(
			'onclick'=>'$("#profileComment").dialog("open"); return false;',
			));


$this->widget('zii.widgets.CListView', array(
			'dataProvider'=>$dataProvider,
			'itemView'=>'/profileComment/_view',
			)); 

}

echo ' <div style="display: none;">';
$this->beginWidget('zii.widgets.jui.CJuiDialog', array(
			'id'=>'profileComment',
			'options'=>array(
				'width' => '500px',
				'modal' => true,
				'title'=>Yum::t('Write a comment'),
				'autoOpen'=>false,
				),
			));

$this->renderPartial('/profileComment/create', array(
			'model' => new YumProfileComment,
			'profile' => $model));

$this->endWidget('zii.widgets.jui.CJuiDialog');
echo '</div>';
?>


