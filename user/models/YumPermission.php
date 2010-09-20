<?php

class YumPermission extends YumActiveRecord {
	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function tableName() {
		return 'permission';
	}

	public function rules() {
		return array(
			array('principal_id, subordinate_id, type, action, template, comment', 'required'),
			array('principal_id, subordinate_id, action, template', 'numerical', 'integerOnly'=>true),
			array('type', 'length', 'max'=>4),
			array('principal_id, subordinate_id, type, action, template, comment', 'safe', 'on'=>'search'),
		);
	}

	public function relations() {
		return array(
			'principal' => array(self::BELONGS_TO, 'YumUser', 'principal_id'),
			'subordinate' => array(self::BELONGS_TO, 'YumUser', 'subordinate_id'),
			'action' => array(self::BELONGS_TO, 'YumAction', 'action')
		);
	}

	public function attributeLabels()
	{
		return array(
			'principal_id' => Yum::t('Principal'),
			'subordinate_id' => Yum::t('Subordinate'),
			'type' => Yum::t('Type'),
			'action' => Yum::t('Action'),
			'template' => Yum::t('Template'),
			'comment' => Yum::t('Comment'),
		);
	}

	public function __toString() {
		return $this->comment;

	}

	public function search() {
		$criteria=new CDbCriteria;

		$criteria->compare('principal_id', $this->principal_id);
		$criteria->compare('subordinate_id', $this->subordinate_id);
		$criteria->compare('type', $this->type, true);
		$criteria->compare('action', $this->action);
		$criteria->compare('template', $this->template);
		$criteria->compare('comment', $this->comment, true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}
