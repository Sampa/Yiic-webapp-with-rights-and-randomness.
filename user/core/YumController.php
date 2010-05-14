<?php
/**
 * Base controller class
 * @author tomasz.suchanek
 * @since 0.6
 * @package Yum.core
 *
 */
abstract class YumController extends CController
{
	/**
	 * Apply module layout if there is no layout specified
	 * for particular controller
	 * @param CAction the action to be executed.
	 * @return boolean whether the action should be executed.
	 */
	public function beforeAction($action) 
	{
		$allowedByParent=parent::beforeAction($action);
		$this->layout = Yii::app()->controller->module->layout;
		return $allowedByParent && true;
	}	
 	
	/**
	 * Filters aplied to all Yum controllers
	 * @return array
	 */
	public function filters()
	{
		return array(
			'accessControl',
		);
	}	
	
}
?>
