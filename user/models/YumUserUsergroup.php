<?php

class YumUserUsergroup extends YumActiveRecord{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'user_has_usergroup';
	}

	public function rules()
	{
		return array(
			array('user_id, group_id', 'required'),
			array('user_id, group_id', 'length', 'max'=>10),
			array('id, user_id, group_id', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('app', 'ID'),
			'user_id' => Yii::t('app', 'User'),
			'group_id' => Yii::t('app', 'Group'),
		);
	}


	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id', $this->id, true);
		$criteria->compare('user_id', $this->user_id, true);
		$criteria->compare('group_id', $this->group_id, true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}
