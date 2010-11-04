<?php 
Yii::app()->clientScript->registerCssFile(
		Yii::app()->getAssetManager()->publish(
			Yii::getPathOfAlias('YumAssets').'/css/yum.css'));

$module = Yum::module();
$this->beginContent($module->baseLayout);
$this->renderPartial($module->menuView); 

echo '<div id="yumcontent">';
printf('<h2> %s </h2>', $this->title); 
echo $content; 
echo '</div>';

$this->endContent(); ?>
