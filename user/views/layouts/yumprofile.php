<?php 
$module = Yii::app()->getModule('user');
$this->beginContent($module->baseLayout);

echo $content; 

$this->endContent(); ?>
