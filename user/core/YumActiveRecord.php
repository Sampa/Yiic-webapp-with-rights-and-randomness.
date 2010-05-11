<?php
/**
 * Base class for all active records
 * @author tomasz.suchanek
 * @since 0.6
 * @package Yum.core
 *
 */
abstract class YumActiveRecord extends CActiveRecord
{
	
	/**
	 * @return array
	 */
	public function behaviors() {
		return array( 'CAdvancedArBehavior'=>array(
			'class' => 'YumModule.components.CAdvancedArBehavior'
		));
	}	
	
}
?>