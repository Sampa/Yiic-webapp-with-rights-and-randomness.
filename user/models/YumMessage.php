<?php

/**
 * This is the model class for table "{{messages}}".
 *
 * The followings are the available columns in table '{{messages}}':
 * @property integer $id
 * @property integer $from_user_id
 * @property integer $to_user_id
 * @property string $title
 * @property string $message
 * @property integer $message_read
 * @property integer $draft
 * 
 * Relations:
 * @property YumUser $to_user
 * @property YumUser $from_user
 */
class YumMessage extends YumActiveRecord
{
	const MSG_NONE = 'None';
	const MSG_PLAIN = 'Plain';
	const MSG_DIALOG = 'Dialog';
	/**
	 * @param string $className
	 * @return YumMessage
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function beforeSave() {
		$this->timestamp = time();
		return true;
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
		if (isset(Yum::module()->messagesTable))
			$this->_tableName = Yum::module()->messagesTable;
		elseif (isset(Yii::app()->modules['user']['messagesTable'])) 
			$this->_tableName = Yii::app()->modules['user']['messagesTable'];
		else
			$this->_tableName = '{{messages}}'; // fallback if nothing is set

		return Yum::resolveTableName($this->_tableName,$this->getDbConnection());
	}

	public function rules()
	{
		return array(
				array('from_user_id, to_user_id, title', 'required'),
				array('from_user_id, draft, message_read, answered', 'numerical', 'integerOnly'=>true),
				array('title', 'length', 'max'=>255),
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

	public function getStatus() {
		if($this->from_user_id == Yii::app()->user->id)
			return Yum::t('sent');
		if($this->answered)
			return Yum::t('answered');
		if($this->message_read)
			return Yum::t('read');
		else
			return Yum::t('new');
	}

	public function scopes() {
		return array(
				'all' => array(
					'condition' => 'to_user_id = '.Yii::app()->user->id .
					               ' or from_user_id = ' . Yii::app()->user->id), 
				'read' => array(
					'condition' => 'to_user_id = '.Yii::app()->user->id . '& message_read'),
				'sent' => array(
					'condition' => 'from_user_id = '.Yii::app()->user->id),
				'unread' => array(
					'condition' => 'to_user_id = '.Yii::app()->user->id . '& !message_read'),
				'answered' => array(
					'condition' => 'to_user_id = '.Yii::app()->user->id . '& answered'),
				);
	}

	public function getDate()
	{
		return date(Yii::app()->getModule('user')->dateTimeFormat, $this->timestamp);
	}


	public function relations()
	{
		return array(
			'from_user' => array(self::BELONGS_TO, 'YumUser', 'from_user_id'),
			'to_user' => array(self::BELONGS_TO, 'YumUser', 'to_user_id'),
			);
	}

	public function attributeLabels()
	{
		return array(
				'id' => Yii::t('UserModule.user', '#'),
				'from_user_id' => Yii::t('UserModule.user', 'From'),
				'to_user_id' => Yii::t('UserModule.user', 'To'),
				'title' => Yii::t('UserModule.user', 'Title'),
				'message' => Yii::t('UserModule.user', 'Message'),
				);
	}

}
