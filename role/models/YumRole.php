<?php
/**
 * This is the model class for table "{{roles}}".
 *
 * The followings are the available columns in table '{{roles}}':
 * @property integer $id
 * @property string $title
 * @property string $description
 * 
 * Relations
 * @property array $users array of YumUser
 */
class YumRole extends YumActiveRecord {
	private $_userRoleTable;
	private $_roleRoleTable;

	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Returns resolved table name (incl. table prefix when it is set in db configuration)
	 * Following algorith of searching valid table name is implemented:
	 *  - try to find out table name stored in currently used module
	 *  - if not found try to get table name from UserModule configuration
	 *  - if not found user default {{roles}} table name
	 * @return string
	 */		
	public function tableName()
	{
			$this->_tableName = '{{roles}}'; // fallback if nothing is set
	}

	public function rules()
	{
		return array(
				array('title', 'required'),
				array('selectable, searchable, is_membership_possible', 'numerical'),
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
				'activeusers'=>array(self::MANY_MANY, 'YumUser', Yum::module()->userRoleTable . '(role_id, user_id)', 'condition' => 'status = 3'),
				'users'=>array(self::MANY_MANY, 'YumUser', Yum::module()->userRoleTable . '(role_id, user_id)'),
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
