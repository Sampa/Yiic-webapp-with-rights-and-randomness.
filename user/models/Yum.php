<?php
/**
 * Helper class
 * @author tomasz.suchanek@gmail.com
 * @since 0.6
 * @package Yum.core
 *
 */
class Yum { 
	/** Register an asset file of Yum */
	public static function register($file) {
		$url = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('YumAssets'));

		$path = $url . '/' . $file;
		if(strpos($file, 'js') !== false)
			Yii::app()->clientScript->registerScriptFile($path);
		else if(strpos($file, 'css') !== false)
			Yii::app()->clientScript->registerCssFile($path);
	}

	public static function t($string, $params = array()) {
		return Yii::t('UserModule.user', $string, $params);
	}

	/**
	 * Resolved table name into table name with prefix if needed
		 * @param string $tablename, e.g {{tablename}}
	 * @param CDbConnection $connection
	 * @since 0.6
	 * @return string resolved table name
	 */
	public static function resolveTableName($tablename, CDbConnection $connection=null)
	{
		$dbConnection = $connection instanceof CDbConnection ? $connection : Yii::app()->getModule('db');
		if(isset($dbConnection->tablePrefix) && $dbConnection->tablePrefix != '') 
		{
			if(substr($dbConnection->tablePrefix, -1) == '_') 
				$tablename = $dbConnection->tablePrefix . $tablename;	
			else
				$tablename = $dbConnection->tablePrefix . '_' . $tablename;	
		}
		return $dbConnection->createCommand($tablename)->getText();
	}
	
	/**
	 * Parses url for predefined symbols and returns real routes
	 * Following symbols are allowed:
	 *  - {yum} - points to base path of Yum
	 *  - {users} - points to user controller 
	 *  - {messsages} - points to base messages module
	 *  - {roles} - points to base roles module
	 *  - {profiles} - points to base profile module
	 * @param string $url
	 * @since 0.6
	 * @return string 
	 */
	public static function route($url)
	{
		$yumBaseRoute=YumWebModule::yum()->yumBaseRoute;
		$tr=array();
		$tr['{yum}']=$yumBaseRoute;
		$tr['{messages}']=$yumBaseRoute.'/messages';
		$tr['{roles}']=$yumBaseRoute.'/role';
		$tr['{profiles}']=$yumBaseRoute.'/profiles';
		$tr['{user}']=$yumBaseRoute.'/user';
		if(is_array($url))
		{
			$ret=array();
			foreach($url as $k=>$entry)
				$ret[$k]=strtr($entry,$tr);
			return $ret;
		}
		else
			return strtr($url,$tr);
		
	}
	
	/**
	 * Produces note: "Field with * are required"
	 * @since 0.6
	 * @return string 
	 */
	public static function requiredFieldNote()
	{
		return CHtml::tag('p',array('class'=>'note'),Yii::t(
			'UserModule.user','Fields with <span class="required">*</span> are required.'
		),true);		
	}

}
?>
