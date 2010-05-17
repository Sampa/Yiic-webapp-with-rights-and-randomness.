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
	 * @var array
	 */
	public $breadcrumbs;
	
	/**
	 * @var array
	 */
	public $menu;
	
	/**
	 * @var string
	 */
	public $title='Change me!';
	
	/**
	 * Set it in controller child controller class to use
	 * different layout than module layout 
	 * @var string
	 */
	public $layour=null;	
	

	/**
	 * Apply module layout if there is no layout specified
	 * for particular controller
	 * @param CAction the action to be executed.
	 * @return boolean whether the action should be executed.
	 */
	public function beforeAction($action) 
	{
		$allowedByParent=parent::beforeAction($action);
		if($this->layout===null)
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
