<?php

/**
 * This is the model base class for the table "payment".
 *
 */
class YumPayment extends YumActiveRecord{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'payment';
	}

	public function rules()
	{
		return array(
			array('title, text', 'required'),
			array('id', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			array('id, title, text', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
			'memberships' => array(self::HAS_MANY, 'YumMembership', 'payment_id'),
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('app', 'ID'),
			'title' => Yii::t('app', 'Title'),
			'text' => Yii::t('app', 'Text'),
		);
	}


	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id', $this->id);
		$criteria->compare('title', $this->title, true);
		$criteria->compare('text', $this->text, true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}
