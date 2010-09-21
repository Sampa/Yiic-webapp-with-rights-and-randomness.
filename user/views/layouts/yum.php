<?php 
$module = Yii::app()->getModule('user');

$this->beginContent($module->baseLayout);
$this->renderPartial($this->module->menuView);
echo '<div style="float:left;">';
printf('<h2> %s </h2>', $this->title); 
echo $content; 
echo '</div>';


$this->endContent(); ?>
