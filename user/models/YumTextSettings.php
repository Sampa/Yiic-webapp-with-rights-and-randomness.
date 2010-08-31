<?php

class YumTextSettings extends YumActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
            if (isset(Yii::app()->controller->module->textSettingsTable))
                $this->_tableName = Yii::app()->controller->module->textSettingsTable;
            elseif (isset(Yii::app()->modules['user']['textSettingsTable']))
                $this->_tableName = Yii::app()->modules['user']['textSettingsTable'];
            else
                $this->_tableName = '{{yumtextsettings}}'; // fallback if nothing is set
            return Yum::resolveTableName($this->_tableName, $this->getDbConnection());
	}

	public function rules()
	{
		return array(
			array('text_registration_header, text_registration_footer, text_login_header, text_login_footer, text_email_registration, subject_email_confirmation, text_email_recovery, text_email_activation', 'safe'),
			array('language', 'length', 'max'=>5),
			array('id, language, text_registration_header, text_registration_footer, text_login_header, text_login_footer, text_email_registration, text_email_recovery, text_email_activation', 'safe', 'on'=>'search'),
		);
	}

	public function relations()
	{
		return array();
	}

	public function attributeLabels()
	{
		return array(
			'id' => Yii::t('UserModule.user', 'ID'),
			'language' => Yii::t('UserModule.user', 'Language'),
			'text_registration_header' => Yii::t('UserModule.user', 'Text Registration Header'),
			'text_registration_footer' => Yii::t('UserModule.user', 'Text Registration Footer'),
			'text_login_header' => Yii::t('UserModule.user', 'Text Login Header'),
			'text_login_footer' => Yii::t('UserModule.user', 'Text Login Footer'),
			'text_email_registration' => Yii::t('UserModule.user', 'Text Email Registration'),
			'subject_email_registration' => Yii::t('UserModule.user', 'Subject of Email Registration'),
			'text_email_recovery' => Yii::t('UserModule.user', 'Text Email Recovery'),
			'text_email_activation' => Yii::t('UserModule.user', 'Text Email Activation'),
		);
	}

	public function search()
	{
		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id);

		$criteria->compare('language',$this->language,true);

		$criteria->compare('text_registration_header',$this->text_registration_header,true);

		$criteria->compare('text_registration_footer',$this->text_registration_footer,true);

		$criteria->compare('text_login_header',$this->text_login_header,true);

		$criteria->compare('text_login_footer',$this->text_login_footer,true);

		$criteria->compare('text_email_registration',$this->text_email_registration,true);

		$criteria->compare('text_email_recovery',$this->text_email_recovery,true);

		$criteria->compare('text_email_activation',$this->text_email_activation,true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria'=>$criteria,
		));
	}
}
