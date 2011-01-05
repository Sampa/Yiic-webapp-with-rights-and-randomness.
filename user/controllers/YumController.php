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
	public $breadcrumbs = array();
	public $menu = array();
	public $title ='';

	public function renderMenu() {
		if(Yii::app()->user->isAdmin())
			$this->widget('AdminMenu');
		else if(!Yii::app()->user->isGuest)
			$this->widget('UserMenu');
	}


	public function filters()
	{
		return array(
			'accessControl',
		);
	}	
}
?>
