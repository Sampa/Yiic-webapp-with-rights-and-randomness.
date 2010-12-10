<?php 
$module = Yii::app()->getModule('user');
$this->beginContent($module->baseLayout);

$this->renderPartial($module->menuView); 
echo $content; 

$this->endContent(); ?>
