<?php
/**
 * Base class for all active records when using Mongo DB
 */
abstract class YumMongoRecord extends EMongoDocument {
	protected $_tableName;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}
}
?>
