<?php 

$module = Yum::module();
$this->beginContent($module->baseLayout);
$this->renderPartial($module->menuView); 

echo '<div id="yumcontent" style="width:70%;margin:5px;">';
printf('<h2> %s </h2>', $this->title); 
echo $content; 
echo '</div>';

$this->endContent(); ?>
