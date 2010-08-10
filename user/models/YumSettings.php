<?php

class YumSettings extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		return 'yumsettings';
	}

	public function getActive()
	{
		return YumSettings::model()->find('is_active')->id;
	}

	public function rules()
	{
		return array(
			array('title, loginType, messageSystem, mail_send_method', 'required'),
			array('password_expiration_time, preserveProfiles, enableRegistration, enableRecovery, enableEmailActivation, enableProfileHistory, readOnlyProfiles, enableCaptcha', 'numerical', 'integerOnly'=>true),
			array('title', 'length', 'max'=>255),
			array('loginType', 'length', 'max'=>26),
			array('id, title, preserveProfiles, enableRegistration, enableRecovery, enableEmailActivation, enableProfileHistory, readOnlyProfiles, loginType, enableCaptcha', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array(
		);
	}

	public function behaviors()
	{
		return array(
			'CAdvancedArBehavior' => array(
				'class' => 'ext.CAdvancedArBehavior'
			)
		);
	}

	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('UserModule.user', 'ID'),
			'title' => Yii::t('UserModule.user', 'Title'),
			'preserveProfiles' => Yii::t('UserModule.user', 'Preserve Profiles'),
			'enableRegistration' => Yii::t('UserModule.user', 'Enable Registration'),
			'enableRecovery' => Yii::t('UserModule.user', 'Enable Recovery'),
			'enableEmailActivation' => Yii::t('UserModule.user', 'Enable Email Activation'),
			'enableProfileHistory' => Yii::t('UserModule.user', 'Enable Profile History'),
			'messageSystem' => Yii::t('UserModule.user', 'Messaging system'),
			'readOnlyProfiles' => Yii::t('UserModule.user', 'Read Only Profiles'),
			'loginType' => Yii::t('UserModule.user', 'Login Type'),
			'enableCaptcha' => Yii::t('UserModule.user', 'Enable Captcha'),
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);

		$criteria->compare('title',$this->title,true);

		$criteria->compare('preserveProfiles',$this->preserveProfiles);

		$criteria->compare('enableRegistration',$this->enableRegistration);

		$criteria->compare('enableRecovery',$this->enableRecovery);

		$criteria->compare('enableEmailActivation',$this->enableEmailActivation);

		$criteria->compare('enableProfileHistory',$this->enableProfileHistory);

		$criteria->compare('readOnlyProfiles',$this->readOnlyProfiles);

		$criteria->compare('loginType',$this->loginType,true);

		$criteria->compare('enableCaptcha',$this->enableCaptcha);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}
