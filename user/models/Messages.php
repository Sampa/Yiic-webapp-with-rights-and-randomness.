<?php

class Messages extends CActiveRecord
{
	private $_tableName;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Returns resolved table name (incl. table prefix when it is set in db configuration)
	 * Following algorith of searching valid table name is implemented:
	 *  - try to find out table name stored in currently used module
	 *  - if not found try to get table name from UserModule configuration
	 *  - if not found user default {{message}} table name
	 * @return string
	 */
  	public function tableName()
  	{
    	if (isset(Yii::app()->controller->module->messagesTable))
      		$this->_tableName = Yii::app()->controller->module->messagesTable;
    	elseif (isset(Yii::app()->modules['user']['messagesTable'])) 
      		$this->_tableName = Yii::app()->modules['user']['messagesTable'];
    	else
      		$this->_tableName = '{{messages}}'; // fallback if nothing is set

		return YumHelper::resolveTableName($this->_tableName,$this->getDbConnection());
  	}

	public function rules()
	{
		return array(
				array('from_user_id, to_user_id, title', 'required'),
				array('from_user_id, draft, message_read', 'numerical', 'integerOnly'=>true),
				array('title', 'length', 'max'=>45),
				array('message', 'safe'),
				);
	}

	public function getTitle()
	{
		if($this->message_read)
			return $this->title;
		else
			return '<strong>' . $this->title . '</strong>';
	}

	public function relations()
	{
		return array(
				'from_user' => array(self::BELONGS_TO, 'User', 'from_user_id'),
				'to_user' => array(self::BELONGS_TO, 'User', 'to_user_id'),
				);
	}

	public function attributeLabels()
	{
		return array(
				'id' => '#',
				'from_user_id' => Yii::t('UserModule.user', 'From'),
				'to_user_id' => Yii::t('UserModule.user', 'To'),
				'title' => Yii::t('UserModule.user', 'Title'),
				'message' => Yii::t('UserModule.user', 'Message'),
				);
	}

}
