<?php
/**
 * This is the model class for table "{{users}}".
 *
 * The followings are the available columns in table '{{users}}':
 * @property integer $id
 * @property string $username
 * @property string $password
 * @property string $activationKey
 * @property integer $createtime
 * @property integer $lastvisit
 * @property integer $superuser
 * @property integer $status
 * 
 * Relations
 * @property YumProfile $profile
 * @property array $roles array of YumRole
 * @property array $users array of YumUser
 * 
 * Scopes:
 * @property YumUser $active
 * @property YumUser $notactive
 * @property YumUser $banned
 * @property YumUser $superuser
 * 
 */
class YumUser extends YumActiveRecord
{
	const STATUS_NOTACTIVE = 0;
	const STATUS_ACTIVE = 1;
	const STATUS_BANNED = -1;

	public $username;
	public $password;
	public $email;
	private $_userRoleTable;
	private $_userUserTable;
	
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	public function search() 
	{
		$criteria=new CDbCriteria;

		$criteria->compare('t.id',$this->id,true);
		$criteria->compare('t.username',$this->username,true);
		$criteria->compare('t.status',$this->status);
		$criteria->compare('t.superuser',$this->superuser);
		$criteria->compare('t.createtime',$this->createtime, true);
		$criteria->compare('t.lastvisit',$this->lastvisit, true);

		return new CActiveDataProvider(get_class($this), array(
					'criteria'=>$criteria,
					'pagination' => array('pageSize' => 20),
					));
	}

	public function beforeValidate() {
		$file = CUploadedFile::getInstanceByName('YumUser[avatar]');
		if($file instanceof CUploadedFile)
			$this->avatar = $file;
		else
			if($this->scenario == 'avatarUpload') 
				$this->avatar = NULL;

		return true;
  }
	
	public function beforeSave() {
		if(Yii::app()->getModule('user')->enableAvatars) 
			$this->updateAvatar();

		return true;
	}

	public function getAdministerableUsers() {
		$users = array();
		$users = $this->users;
		foreach($this->roles as $role) {
			if($role->roles)
				foreach($role->roles as $role) 
					$users = array_merge($this->users, $role->users);
		}

		return $users;
	}
	/**
	 * Returns resolved table name (incl. table prefix when it is set in db configuration)
	 * Following algorith of searching valid table name is implemented:
	 *  - try to find out table name stored in currently used module
	 *  - if not found try to get table name from UserModule configuration
	 *  - if not found user default {{users}} table name
	 * @return string
	 */			
	public function tableName()
	{
    	if (isset(Yii::app()->controller->module->usersTable))
      		$this->_tableName = Yii::app()->controller->module->usersTable;
    	elseif (isset(Yii::app()->modules['user']['usersTable'])) 
      		$this->_tableName = Yii::app()->modules['user']['usersTable'];
    	else
      		$this->_tableName = '{{users}}'; // fallback if nothing is set

		return Yum::resolveTableName($this->_tableName,$this->getDbConnection());
	}

	public function rules()
	{
		$passwordRequirements = Yii::app()->getModule('user')->passwordRequirements;

		$passwordrule = array_merge(array('password', 'YumPasswordValidator'), 
				$passwordRequirements);

		$rules[] = $passwordrule;
		$rules[] = array('username', 'length', 'max'=>20, 'min' => 3,
				'message' => Yum::t("Incorrect username (length between 3 and 20 characters)."));
		if(Yii::app()->getModule('user')->loginType != 'LOGIN_BY_EMAIL')
			$rules[] = array('username', 'unique', 'message' => Yii::t("UserModule.user", "This user's name already exists."));
		$rules[] = array('username', 'match', 'pattern' => '/^[A-Za-z0-9_]+$/u','message' => Yii::t("UserModule.user", "Incorrect symbol's. (A-z0-9)"));
		$rules[] = array('status', 'in', 'range'=>array(0,1,-1));
		$rules[] = array('superuser', 'in', 'range'=>array(0,1));
		$rules[] = array('username, createtime, lastvisit, lastpasswordchange, superuser, status', 'required');
		$rules[] = array('password', 'required', 'on'=>array('insert'));
		$rules[] = array('createtime, lastvisit, superuser, status', 'numerical', 'integerOnly'=>true);

		if(Yii::app()->getModule('user')->enableAvatars)  {
			$rules[] = array('avatar', 'required', 'on' => 'avatarUpload');
			$rules[] = array('avatar', 'EPhotoValidator',
					'allowEmpty' => true,
					'mimeType' => array('image/jpeg', 'image/png', 'image/gif'),
					'maxWidth' => 200,
					'maxHeight' => 200,
					'minWidth' => 50, 
					'minHeight' => 50);
		}
		return $rules;
	}

	public function hasRole($role_title) {
		foreach($this->roles as $role)
			if($role->id == $role_title || $role->title == $role_title)
				return true;

		return false;
	}

	public function getRoles() {
		$roles = '';
		foreach($this->roles as $role)
			$roles .= ' ' . $role->title;

		return $roles;
	}

	public function relations()
	{
    if (isset(Yii::app()->controller->module->userRoleTable))
      $this->_userRoleTable = Yii::app()->controller->module->userRoleTable;
    elseif (isset(Yii::app()->modules['user']['userRoleTable'])) 
      $this->_tableName = Yii::app()->modules['user']['userRoleTable'];
    else
      $this->_userRoleTable = '{{user_has_role}}';

    if (isset(Yii::app()->controller->module->userUserTable))
      $this->_userUserTable = Yii::app()->controller->module->userUserTable;
    elseif (isset(Yii::app()->modules['user']['userUserTable'])) 
      $this->_tableName = Yii::app()->modules['user']['userUserTable'];
    else
      $this->_userUserTable = '{{user_has_user}}';
      
    // resolve table names to use them in relations definition
    $relationUHRTableName=Yum::resolveTableName($this->_userRoleTable,$this->getDbConnection());      
    $relationUHUTableName=Yum::resolveTableName($this->_userUserTable,$this->getDbConnection());

	return array(
		'messages'=>array(self::HAS_MANY, 'YumMessage', 'to_user_id', 'order' => 'messages.id DESC'),
		'profile'=>array(self::HAS_MANY, 'YumProfile', 'user_id', 'order' => 'profile.profile_id DESC'),
		'roles'=>array(self::MANY_MANY, 'YumRole', $relationUHRTableName . '(user_id, role_id)'),
		'users'=>array(self::MANY_MANY, 'YumUser', $relationUHUTableName . '(owner_id, child_id)'),
		'visits' => array(self::HAS_MANY, 'YumProfileVisit', 'visited_id')
	);
	}

	public function register($username=null, $password=null, $email=null) {
		if($username!==null && $password!==null) {
			// Password equality is checked in Registration Form
			$this->username = $username;
			$this->password = $this->encrypt($password);
		}

		foreach(YumProfile::model()->findAll() as $profile) {
			if($email == $profile->email)
				return false;
		}

		$this->activationKey = $this->generateActivationKey(false,$password);
		$this->createtime = time();
		$this->superuser = 0;

		switch(Yii::app()->getModule('user')->registrationType) {
			case YumRegistration::REG_SIMPLE:
				$this->status = YumUser::STATUS_ACTIVE;
				break;
			case YumRegistration::REG_EMAIL_CONFIRMATION:
			case YumRegistration::REG_CONFIRMATION_BY_ADMIN:
				$this->status = YumUser::STATUS_NOTACTIVE;
				break;
			case YumRegistration::REG_EMAIL_AND_ADMIN_CONFIRMATION:
				// Users stay banned until they confirm their email address.
				$this->status = YumUser::STATUS_BANNED;
				break;
		}

		return $this->save();
	}

	public function isPasswordExpired() 
	{
		$distance = Yii::app()->getModule('user')->password_expiration_time * 60 * 60;
		return $this->lastpasswordchange - $distance > time();
	}

	/**
	 * Activation of an user account
	 */
	public function activate($email, $activationKey)
	{
		$find = YumProfile::model()->findByAttributes(array('email'=>$email))->user;
		if ($find->status) {
			return true;
		} else if($find->activationKey == $activationKey) {
			$find->activationKey = $find->generateActivationKey(true);
			$find->status = 1;
			$find->save();
			return true;
		} else
			return false;
	}
	/**
	 * @params boolean $activate Whether to generate activation key when user is registering first time (false)
	 * or when it is activating (true)
	 * @params string $password password entered by user	
	 * @param array $params, optional, to allow passing values outside class in inherited classes
	 * By default it uses password and microtime combination to generated activation key
	 * When user is activating, activation key becomes micortime()
	 * @return string
	 */
	public function generateActivationKey($activate=false, $password='', array $params=array())
	{
		return $activate ? $this->encrypt(microtime()) : $this->encrypt(microtime() . $this->password);
	}

	public function attributeLabels()
	{
		return array(
			'id'=>Yum::t('#'),
			'username'=>Yum::t("Username"),
			'password'=>Yum::t("Password"),
			'verifyPassword'=>Yum::t("Retype password"),
			'verifyCode'=>Yum::t("Verification code"),
			'activationKey' => Yum::t("Activation key"),
			'createtime' => Yum::t("Registration date"),
			'lastvisit' => Yum::t("Last visit"),
			'superuser' => Yum::t("Superuser"),
			'status' => Yum::t("Status"),
			'avatar' => Yum::t("Avatar image"),
		);
	}
	
	/**
	 * This function is used for password encryption.
	 * @return hash string.
	 */
	public static function encrypt($string = "")
	{
		$salt = YumWebModule::yum()->salt;
		$hashFunc = YumWebModule::yum()->hashFunc;
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
	 * Get all users with a specified role.
	 * @param string $roleTitle title of role to be searched
	 * @return array users with specified role. Null if none are found.
	 */
	public static function getUsersByRole($roleTitle)
	{
		$role = Role::model()->findByAttributes(array('title'=>$roleTitle));
		return $role ? $role->users : null;
	}

	/**
	 * Return admins.
	 * @return array syperusers names
	 */	
	public static function getAdmins() {
		$admins = YumUser::model()->active()->superuser()->findAll();
		$returnarray = array();
		foreach ($admins as $admin)
			array_push($returnarray, $admin->username);
		return $returnarray;
	}

	public function updateAvatar() {
		if($this->avatar !== NULL && isset($_FILES['YumUser'])) {
			// prepend user id to avoid conflicts when two users upload an avatar with the
			// same file name
			$filename = $this->id . '_' . $_FILES['YumUser']['name']['avatar'];
			if(is_object($this->avatar)) {
				$this->avatar->saveAs(Yii::app()->getModule('user')->avatarPath . '/' . $filename);
				$this->avatar = $filename;
			}
		}
	}

		public function renderAvatar() {
			if($this->avatar)
				echo CHtml::image(Yii::app()->baseUrl . '/' . Yii::app()->getModule('user')->avatarPath . '/' . $this->avatar);
		}


	
}
