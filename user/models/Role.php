<?php

class Role extends CActiveRecord
{
	private $_tableName;
	private $_userRoleTable;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function behaviors() {
		return array( 'CAdvancedArBehavior' => array(
			'class' => 'application.modules.user.components.CAdvancedArBehavior'));
	}

  public function tableName()
  {
    if (isset(Yii::app()->controller->module->rolesTable))
      $this->_tableName = Yii::app()->controller->module->rolesTable;
    elseif (isset(Yii::App()->modules['user']['rolesTable'])) 
      $this->_tableName = Yii::App()->modules['user']['rolesTable'];
    else
      $this->_tableName = 'roles';

		return $this->_tableName;
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
    if (isset(Yii::app()->controller->module->userRoleTable))
      $this->_userRoleTable = Yii::app()->controller->module->userRoleTable;
    elseif (isset(Yii::App()->modules['user']['userRoleTable'])) 
      $this->_tableName = Yii::App()->modules['user']['userRoleTable'];
    else
      $this->_userRoleTable = 'user_has_role';

		return array(
				'users'=>array(self::MANY_MANY, 'User', $this->_userRoleTable .'(role_id, user_id)'),
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
