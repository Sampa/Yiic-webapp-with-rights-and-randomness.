<?php
/**
 * This is the model class for table "{{roles}}".
 *
 * The followings are the available columns in table '{{roles}}':
 * @property integer $id
 * @property string $title
 * @property string $description
 * 
 * Relations
 * @property array $users array of YumUser
 */
class YumRole extends YumActiveRecord
{
	private $_userRoleTable;
	private $_roleRoleTable;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Returns resolved table name (incl. table prefix when it is set in db configuration)
	 * Following algorith of searching valid table name is implemented:
	 *  - try to find out table name stored in currently used module
	 *  - if not found try to get table name from UserModule configuration
	 *  - if not found user default {{roles}} table name
	 * @return string
	 */		
  	public function tableName()
  	{
  		if (isset(Yii::app()->controller->module->rolesTable))
      		$this->_tableName = Yii::app()->controller->module->rolesTable;
    	elseif (isset(Yii::app()->modules['user']['rolesTable'])) 
      		$this->_tableName = Yii::app()->modules['user']['rolesTable'];
    	else
      		$this->_tableName = '{{roles}}'; // fallback if nothing is set
    	return Yum::resolveTableName($this->_tableName,$this->getDbConnection());
  }

	public function rules()
	{
		return array(
			array('title', 'required'),
			array('title, description', 'length', 'max' => '255'),
		);
	}

	public function relations()
	{
		return array(
				'users'=>array(self::MANY_MANY, 'YumUser', Yii::app()->getModule('user')->userRoleTable . '(role_id, user_id)'),
				'roles'=>array(self::MANY_MANY, 'YumRole', Yii::app()->getModule('user')->roleRoleTable . '(role_id, child_id)'),
				);
	}

	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t("UserModule.user", "#"),
			'title'=>Yii::t("UserModule.user", "Title"),
			'description'=>Yii::t("UserModule.user", "Description"),
		);
	}
}
