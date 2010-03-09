<?php

class Role extends CActiveRecord
{
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		if(is_object(Yii::app()->controller->module)) {
			return Yii::app()->controller->module->rolesTable
				? Yii::app()->controller->module->rolesTable
				: 'roles';
		} else
			return 'roles';
	}

	public function rules()
	{
		return array(
			array('title', 'required'),
			array('title, description', 'length', 'max' => '255')
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
			'users'=>array(self::MANY_MANY, 'User', 'user_has_role(role_id, user_id)'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>Yii::t("UserModule.user", "#"),
			'title'=>Yii::t("UserModule.user", "Title"),
			'description'=>Yii::t("UserModule.user", "Description"),
		);
	}
}
