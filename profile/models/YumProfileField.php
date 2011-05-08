<?php
/**
 * This is the model class for table "{{profile_fields}}".
 *
 * The followings are the available columns in table '{{profile_fields}}':
 * Fields:
 * @property integer $id
 * @property integer $field_group_id
 * @property string $varname
 * @property string $title
 * @property string $hint
 * @property string $field_type
 * @property integer $field_size
 * @property integer $field_size_min
 * @property integer $required
 * @property string $match
 * @property string $range
 * @property string $error_message
 * @property string $other_validator
 * @property string $default
 * @property integer $position
 * @property integer $visible
 * 
 * Relations
 * @property YumProfileFieldsGroup $group
 * 
 * Scopes:
 * @method YumProfileField forAll
 * @method YumProfileField forUser
 * @method YumProfileField forOwner
 * @method YumProfileField forRegistration
 * @method YumProfileField sort
 */
class YumProfileField extends YumActiveRecord
{
	const VISIBLE_NO=0;
	const VISIBLE_ONLY_OWNER=1;
	const VISIBLE_REGISTER_USER=2;
	const VISIBLE_USER_DECISION=3;
	const VISIBLE_ALL=4;

	/**
     * Returns the static model of the specified AR class.
	 * @param string $className
	 * @return YumProfileField
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function isPublic($user = null) {
		if($user == null)
			$user = Yii::app()->user->id;

		if(!$this->visible)
			return false;

		if($privacy = YumUser::model()->findByPk($user)->privacy) {
			if($privacy->public_profile_fields & pow(2, $this->id))
				return true;
		}

		return false;
	}


	/**
	 * Returns resolved table name (incl. table prefix when it is set in db configuration)
	 * Following algorith of searching valid table name is implemented:
	 *  - try to find out table name stored in currently used module
	 *  - if not found try to get table name from UserModule configuration
	 *  - if not found user default {{profile_fields}} table name
	 * @return string
	 */	
  	public function tableName()
  	{
    	if (isset(Yum::module()->profileFieldsTable))
      		$this->_tableName = Yum::module()->profileFieldsTable;
    	elseif (isset(Yii::app()->modules['user']['profileFieldsTable']))
      		$this->_tableName = Yii::app()->modules['user']['profileFieldsTable'];
    	else
      		$this->_tableName = '{{profile_fields}}'; // fallback if nothing is set

    	return Yum::resolveTableName($this->_tableName,$this->getDbConnection());
  	}

	public function rules()
	{
		return array(
			array('varname, title, field_type', 'required'),
			array('varname', 'match', 'pattern' => '/^[a-z_0-9]+$/u','message' => Yii::t("UserModule.user", "Incorrect symbol's. (a-z)")),
			array('varname', 'unique', 'message' => Yii::t("UserModule.user", "This field already exists.")),
			array('varname, field_type', 'length', 'max'=>50),
			array('field_group_id, field_size, field_size_min, required, position, visible', 'numerical', 'integerOnly'=>true),
			array('hint','safe'),
			array('related_field_name, title, match, range, error_message, other_validator, default', 'length', 'max'=>255),
		);
	}

	public function relations()
	{
		$relations = array(
			'group'=>array(self::BELONGS_TO, 'YumProfileFieldsGroup', 'field_group_id')
		);

		return $relations;
	}

	public function attributeLabels()
	{
		return array(
			'id' => Yum::t('#'),
			'field_group_id' => Yum::t('Field group'),
			'varname' => Yum::t('Variable name'),
			'title' => Yum::t('Title'),
			'hint' => Yum::t('Hint'),
			'field_type' => Yum::t('Field type'),
			'field_size' => Yum::t('Field size'),
			'field_size_min' => Yum::t('Field size min'),
			'required' => Yum::t('Required'),
			'match' => Yum::t('Match'),
			'range' => Yum::t('Range'),
			'error_message' => Yum::t('Error message'),
			'other_validator' => Yum::t('Other validator'),
			'default' => Yum::t('Default'),
			'position' => Yum::t('Position'),
			'visible' => Yum::t('Visible'),
		);
	}
	
	public function scopes()
    {
        return array(
            'forAll'=>array(
                'condition'=>'visible='.self::VISIBLE_ALL,
            ),
            'forUser'=>array(
                'condition'=>'visible>='.self::VISIBLE_REGISTER_USER,
            ),
            'forOwner'=>array(
                'condition'=>'visible>='.self::VISIBLE_ONLY_OWNER,
            ),
            'forRegistration'=>array(
                'condition'=>'required>0',
            ),
            'sort'=>array(
                'order'=>'field_group_id ASC, t.position ASC',
            ),
            
        );
    }

	
	public static function itemAlias($type,$code=NULL) {
		$_items = array(
			'field_type' => array(
				'INTEGER' => Yum::t('INTEGER'),
				'VARCHAR' => Yum::t( 'VARCHAR'),
				'TEXT'=> Yum::t( 'TEXT'),
				'DATE'=> Yum::t( 'DATE'),
				'DROPDOWNLIST' => Yum::t('DROPDOWNLIST'),
				'FLOAT'=> Yum::t('FLOAT'),
				'BOOL'=> Yum::t('BOOL'),
				'BLOB'=> Yum::t('BLOB'),
				'BINARY'=> Yum::t('BINARY'),
				'FILE'=> 'FILE',
			),
			'required' => array(
				'0' => Yum::t('No'),
				'2' => Yum::t('No, but show on registration form'),
				'1' => Yum::t('Yes and show on registration form'),
			),
			'visible' => array(
				self::VISIBLE_USER_DECISION => Yum::t('Let the user choose in privacy settings'),
				self::VISIBLE_ALL => Yum::t('For all'),
				self::VISIBLE_REGISTER_USER => Yum::t('Registered users'),
				self::VISIBLE_ONLY_OWNER => Yum::t('Only owner'),
				'0' => Yum::t('Hidden'),
			),
		);
		if (isset($code))
			return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
		else
			return isset($_items[$type]) ? $_items[$type] : false;
	}
}
