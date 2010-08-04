<?php 
$this->beginContent(Yii::app()->getModule('user')->baseLayout);

if(!empty($this->menu))  {

echo '<div style="float:right; width:25%; margin: 0px 5px 0px 5px;">';
$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>Yii::t('UserModule.user', 'User Operations' )));
$this->widget('zii.widgets.CMenu', array( 'items'=>$this->menu ));
$this->endWidget();
echo '</div>';
}

echo '<div id="yumcontent" style="width:70%;margin:5px;">';
printf('<h2> %s </h2>', $this->title); 
echo $content; 
echo '</div>';


$this->endContent(); ?>
