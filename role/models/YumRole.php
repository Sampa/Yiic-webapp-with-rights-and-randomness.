<?php
class YumRole extends YumActiveRecord {
	private $_userRoleTable;
	private $_roleRoleTable;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function tableName()
	{
		if (isset(Yum::module('role')->rolesTable))
			$this->_tableName = Yum::module('role')->rolesTable;
		else
			$this->_tableName = '{{roles}}'; // fallback if nothing is set

		return Yum::resolveTableName($this->_tableName,$this->getDbConnection());
	}

	public function rules()
	{
		return array(
				array('title', 'required'),
				array('is_membership_possible', 'numerical'),
				array('price', 'numerical'),
				array('duration', 'numerical'),
				array('title, description', 'length', 'max' => '255'),
				);
	}

	public function scopes() {
		return array(
				'possible_memberships' => array('condition' => 'is_membership_possible = 1'),
				);
	}

	public function relations()
	{
		return array(
				'activeusers'=>array(self::MANY_MANY, 'YumUser', Yum::module('role')->userHasRoleTable . '(role_id, user_id)', 'condition' => 'status = 3'),
				'users'=>array(self::MANY_MANY, 'YumUser', 'user_has_role(role_id, user_id)'),
				'permissions' => array(self::HAS_MANY, 'YumPermission', 'principal_id'),
				'memberships' => array(self::HAS_MANY, 'YumMembership', 'membership_id'),
				'managed_by' => array(self::HAS_MANY, 'YumPermission', 'subordinate_id'),

				);
	}

	public function activeUsers() {
		$users = $this->users;
		foreach($users as $key => $user)
			if(!$user->active())
				unset($users[$key]);

		return $users;
	}

	public function attributeLabels()
	{
		return array(
				'id'=>Yum::t("#"),
				'title'=>Yum::t("Title"),
				'description'=>Yum::t("Description"),
				'selectable'=>Yum::t("Selectable on registration"),
				'searchable'=>Yum::t("Searchable"),
				'price'=>Yum::t("Price"),
				'duration'=>Yum::t("Duration in days"),
				);
	}
}
