<?php 
$module = Yii::app()->getModule('user');
$this->beginContent($module->baseLayout);
$this->renderPartial('/user/menu');

echo '<div id="yumcontent" style="width:70%;margin:5px;">';
printf('<h2> %s </h2>', $this->title); 
echo $content; 
echo '</div>';


$this->endContent(); ?>
