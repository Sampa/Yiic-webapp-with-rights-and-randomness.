<?php

class User extends CActiveRecord
{
	const STATUS_NOTACTIVE = 0;
	const STATUS_ACTIVE = 1;
	const STATUS_BANNED = -1;

	public $username;
	public $password;
	public $email;
	private $_tableName;
	private $_userRoleTable;
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function behaviors() 
	{
		return array( 'CAdvancedArBehavior' => array(
					'class' => 'application.modules.user.components.CAdvancedArBehavior'));
	}

	public function tableName()
	{
    if (isset(Yii::app()->controller->module->usersTable))
      $this->_tableName = Yii::app()->controller->module->usersTable;
    elseif (isset(Yii::app()->modules['user']['usersTable'])) 
      $this->_tableName = Yii::app()->modules['user']['usersTable'];
    else
      $this->_tableName = 'users'; // fallback if nothing is set

		return $this->_tableName;
	}

	public function rules()
	{
		return array(
			array('username', 'length', 'max'=>20, 'min' => 3,'message' => Yii::t("UserModule.user", "Incorrect username (length between 3 and 20 characters).")),
			array('password', 'length', 'max'=>128, 'min' => 4, 'message' => Yii::t("UserModule.user", "Incorrect password (minimal length 4 symbols).")),
			array('username', 'unique', 'message' => Yii::t("UserModule.user", "This user's name already exists.")),
			array('username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u','message' => Yii::t("UserModule.user", "Incorrect symbol's. (A-z0-9)")),
			array('status', 'in', 'range'=>array(0,1,-1)),
			array('superuser', 'in', 'range'=>array(0,1)),
			array('username, createtime, lastvisit, superuser, status', 'required'),
			array('password', 'required', 'on'=>array('insert')),
			array('createtime, lastvisit, superuser, status', 'numerical', 'integerOnly'=>true),
		);
	}

	public function relations()
	{
    if (isset(Yii::app()->controller->module->userRoleTable))
      $this->_userRoleTable = Yii::app()->controller->module->userRoleTable;
    elseif (isset(Yii::app()->modules['user']['userRoleTable'])) 
      $this->_tableName = Yii::app()->modules['user']['userRoleTable'];
    else
      $this->_userRoleTable = 'user_has_role';

		return array(
			'profile'=>array(self::HAS_ONE, 'Profile', 'user_id', 'order' => 'profile.profile_id DESC'),
			'roles'=>array(self::MANY_MANY, 'Role', $this->_userRoleTable . '(user_id, role_id)'),
			);
	}

	public function register($username, $password, $email)
	{
		// Password equality is checked in Registration Form
		$this->username = $username;
		$this->password = $this->encrypt($password);
		$this->profile->email = $email;
		$this->activationKey = $this->encrypt(microtime() . $password);
		$this->createtime = time();
		$this->superuser = 0;

		if(Yii::app()->controller->module->disableEmailActivation == true) 
			$this->status = User::STATUS_ACTIVE;
		else
			$this->status = User::STATUS_NOTACTIVE;

		$this->lastvisit = ((Yii::app()->user->allowAutoLogin &&
					UserModule::$allowInactiveAcctLogin) ? time() : 0);

		if($this->save()) 
		{
			$profile = new Profile();
			$profile->user_id = $this->id;
			$profile->save();
			return true;
		} 
		else
			return false;

	}

	/**
	 * Activation of an user account
	 */
	public function activate($email, $activationKey)
	{
		$find = Profile::model()->findByAttributes(array('email'=>$email))->user;
		if ($find->status) 
		{
			return true;
		} 
		elseif($find->activationKey == $activationKey) 
		{
			$find->activationKey = User::encrypt(microtime());
			$find->status = 1;
			$find->save();
			return true;
		}
		else
			return false;
	}

	public function attributeLabels()
	{
		return array(
			'username'=>Yii::t("UserModule.user", "username"),
			'password'=>Yii::t("UserModule.user", "password"),
			'verifyPassword'=>Yii::t("UserModule.user", "Retype Password"),
			'verifyCode'=>Yii::t("UserModule.user", "Verification Code"),
			'id' => 'Id',
			'activationKey' => Yii::t("UserModule.user", "activation key"),
			'createtime' => Yii::t("UserModule.user", "Registration date"),
			'lastvisit' => Yii::t("UserModule.user", "Last visit"),
			'superuser' => Yii::t("UserModule.user", "Superuser"),
			'status' => Yii::t("UserModule.user", "Status"),
		);
	}
	
	/**
	 * This function is used for password encryption.
	 * @return hash string.
	 */
	public static function encrypt($string = "")
	{
		$salt = Yii::app()->controller->module->salt;
		$hashFunc = Yii::app()->controller->module->hashFunc;
		$string = sprintf("%s%s%s", $salt, $string, $salt);
		
		if(!function_exists($hashFunc))
			throw new CException('Function `'.$hashFunc.'` is not a valid callback for hashing algorithm.');
		
		return $hashFunc($string);
	}
	
	public function scopes()
	{
		return array(
				'active'=>array(
					'condition'=>'status='.self::STATUS_ACTIVE,
					),
				'notactive'=>array(
					'condition'=>'status='.self::STATUS_NOTACTIVE,
					),
				'banned'=>array(
					'condition'=>'status='.self::STATUS_BANNED,
					),
				'superuser'=>array(
					'condition'=>'superuser=1',
					),
				);
	}

	public static function itemAlias($type,$code=NULL) 
	{
		$_items = array(
				'UserStatus' => array(
					'0' => Yii::t("UserModule.user", 'Not active'),
					'1' => Yii::t("UserModule.user", 'Active'),
					'-1'=> Yii::t("UserModule.user", 'Banned'),
					),
				'AdminStatus' => array(
					'0' => Yii::t("UserModule.user", 'No'),
					'1' => Yii::t("UserModule.user", 'Yes'),
					),
				);
		if (isset($code))
			return isset($_items[$type][$code]) ? $_items[$type][$code] : false;
		else
			return isset($_items[$type]) ? $_items[$type] : false;
	}

	/**
	 * Return admins.
	 * @return array syperusers names
	 */	
	public static function getAdmins() {
		$admins = User::model()->active()->superuser()->findAll();
		$returnarray = array();
		foreach ($admins as $admin)
			array_push($returnarray, $admin->username);
		return $returnarray;
	}
	
}
