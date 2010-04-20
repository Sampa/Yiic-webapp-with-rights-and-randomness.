<?php

class DefaultController extends Controller 
{
	public function actionIndex() 
	{
		$this->redirect($this->module->returnUrl);
	}
}

?>
