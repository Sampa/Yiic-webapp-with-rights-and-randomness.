<?php
/**
 * Helper class
 * @author tomasz.suchanek@gmail.com
 * @since 0.6
 * @package Yum.core
 *
 */
class YumHelper
{
	/**
	 * Resolved table name into table name with prefix if needed
	 * @param string $tablename, e.g {{tablename}}
	 * @param CDbConnection $connection
	 * @since 0.6
	 * @return string resolved table name
	 */
	public static function resolveTableName($tablename, CDbConnection $connection=null)
	{
		$dbConnection=$connection instanceof CDbConnection ? $connection : Yii::app()->getModule('db');
		return $dbConnection->createCommand($tablename)->getText();
	}
}
?>