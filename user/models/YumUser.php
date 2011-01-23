<?php

/**
 * This is the model class for a User in Yum
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
class YumUser extends YumActiveRecord {
	const STATUS_NOTACTIVE = 0;
	const STATUS_ACTIVATED = 1;
	const STATUS_ACTIVE_FIRST_VISIT = 2;
	const STATUS_ACTIVE = 3;
	const STATUS_BANNED = -1;
	const STATUS_REMOVED = -2;

	public $username;
	public $password;
	public $email;
	public $activationKey;
	public $password_changed = false;
	private $_userRoleTable;
	private $_friendshipTable;

	public static function model($className=__CLASS__) {
		return parent::model($className);
	}

	public function isOnline() {
		return $this->lastaction > time() - Yum::module()->offlineIndicationTime;
	}

	// If Online status is enabled, we need to set the timestamp of the
  // last action when a user does something
	public function setLastAction() {
		$this->lastaction = time();
		return $this->save();
	}

	public function logout() {
		if(Yum::module()->enableOnlineStatus && !Yii::app()->user->isGuest) {
			$this->lastaction = 0;
			$this->save('lastaction');
		}
	}

	// This function tries to generate a as human-readable password as possible
	public static function generatePassword() { 
		$consonants = array("b","c","d","f","g","h","j","k","l","m","n","p","r","s","t","v","w","x","y","z"); 
		$vocals = array("a","e","i","o","u"); 

		$password = '';

		srand((double) microtime() * 1000000);
		for ($i = 1; $i <= 4; $i++) {
			$password .= $consonants[rand(0, 19)];
			$password .= $vocals[rand(0, 4)];
		}
		$password .= rand(0, 9);

		return $password;
	}

	// Which memberships are bought by the user
	public function getActiveMemberships() {
		$roles = array();
		foreach($this->memberships as $membership) {
			if($membership->end_date > time())
				$roles[] = $membership->role;
		}

		return $roles;
	}

	public function search() {
		$criteria = new CDbCriteria;

		$criteria->compare('t.id', $this->id, true);
		$criteria->compare('t.username', $this->username, true);
		$criteria->compare('t.status', $this->status);
		$criteria->compare('t.superuser', $this->superuser);
		$criteria->compare('t.createtime', $this->createtime, true);
		$criteria->compare('t.lastvisit', $this->lastvisit, true);

		return new CActiveDataProvider(get_class($this), array(
			'criteria' => $criteria,
			'pagination' => array('pageSize' => 20),
		));
	}

	public function beforeValidate() {

		if ($this->isNewRecord)
			$this->createtime = time();


		return true;
	}

	public function beforeSave() {
		if ($this->password_changed)
			$this->password = YumUser::encrypt($this->password);

		return parent::beforeSave();
	}

	public function setPassword($password) {
		if ($password != '') {
			$this->password = $password;
			$this->lastpasswordchange = time();
			$this->password_changed = true;
			return $this->save();
		}
	}

	public function afterSave() {
		$setting = YumPrivacySetting::model()->findByPk($this->id);
		if (!$setting) {
			$setting = new YumPrivacySetting();
			$setting->user_id = $this->id;
			$setting->save();
		}

		if(Yum::module()->enableLogging)
			YumActivityController::logActivity($this,
					$this->isNewRecord ? 'user_created' : 'user_updated');

		return parent::afterSave();
	}

	/**
	 * Returns resolved table name (incl. table prefix when it is set in db configuration)
	 * Following algorith of searching valid table name is implemented:
	 *  - try to find out table name stored in currently used module
	 *  - if not found try to get table name from UserModule configuration
	 *  - if not found user default {{users}} table name
	 * @return string
	 */
	public function tableName() {
		if (isset(Yum::module()->usersTable))
			$this->_tableName = Yum::module()->usersTable;
		elseif (isset(Yii::app()->modules['user']['usersTable']))
			$this->_tableName = Yii::app()->modules['user']['usersTable'];
		else
			$this->_tableName = '{{users}}'; // fallback if nothing is set

		return Yum::resolveTableName($this->_tableName, $this->getDbConnection());
	}

	public function rules() {
		$passwordRequirements = Yum::module()->passwordRequirements;
		$usernameRequirements = Yum::module()->usernameRequirements;

		$passwordrule = array_merge(array('password', 'YumPasswordValidator'), $passwordRequirements);

		$rules[] = $passwordrule;

		$rules[] = array('username', 'length',
				'max' => $usernameRequirements['maxLen'],
				'min' => $usernameRequirements['minLen'],
				'message' => Yum::t(
					'Username length needs to be between {minLen} and {maxlen} characters', array(
						'{minLen}' => $usernameRequirements['minLen'],
						'{maxLen}' => $usernameRequirements['maxLen'])));

		$rules[] = array('username',
				'unique',
				'message' => Yum::t("This user's name already exists."));
		$rules[] = array(
				'username',
				'match',
				'pattern' => $usernameRequirements['match'],
				'message' => Yum::t($usernameRequirements['dontMatchMessage'])); 
		$rules[] = array('status', 'in', 'range' => array(0, 1, 2, 3, -1, -2));
		$rules[] = array('superuser', 'in', 'range' => array(0, 1));
		$rules[] = array('createtime, lastvisit, lastpasswordchange, superuser, status', 'required');
		$rules[] = array('notifyType, avatar', 'safe');
		$rules[] = array('password', 'required', 'on' => array('insert'));
		$rules[] = array('createtime, lastvisit, lastaction, superuser, status', 'numerical', 'integerOnly' => true);

		if (Yum::module()->enableAvatar) {
			// require an avatar image in the avatar upload screen
			$rules[] = array('avatar', 'required', 'on' => 'avatarUpload');

			// if automatic scaling is deactivated, require the exact size	
			$rules[] = array('avatar', 'EPhotoValidator',
					'allowEmpty' => true,
					'mimeType' => array('image/jpeg', 'image/png', 'image/gif'),
					'maxWidth' => Yum::module()->avatarMaxWidth,
					'maxHeight' => Yum::module()->avatarMaxWidth,
					'minWidth' => 50,
					'minHeight' => 50, 
					'on' => 'avatarSizeCheck'); 
		}
		return $rules;
	}

	public function hasRole($role_title) {
		foreach ($this->roles as $role)
			if ($role->id == $role_title || $role->title == $role_title)
				return true;

		return false;
	}

	public function getRoles() {
		$roles = '';
		foreach ($this->roles as $role)
			$roles .= ' ' . $role->title;

		return $roles;
	}

	public function can($action) {
		foreach ($this->permissions as $permission)
			if ($permission->action->title == $action)
				return true;

		return false;
	}

	public function relations() {
		if (isset(Yum::module()->userRoleTable))
			$this->_userRoleTable = Yum::module()->userRoleTable;
		elseif (isset(Yii::app()->modules['user']['userRoleTable']))
			$this->_tableName = Yii::app()->modules['user']['userRoleTable'];
		else
			$this->_userRoleTable = '{{user_has_role}}';

		if (isset(Yum::module()->friendshipTable))
			$this->_friendshipTable = Yum::module()->friendshipTable;
		elseif (isset(Yii::app()->modules['user']['friendshipTable']))
			$this->_tableName = Yii::app()->modules['user']['friendshipTable'];
		else
			$this->_friendshipTable = '{{friendship}}';

		// resolve table names to use them in relations definition
		$relationUHRTableName = Yum::resolveTableName($this->_userRoleTable, $this->getDbConnection());
		$relationFRSPTableName = Yum::resolveTableName($this->_friendshipTable, $this->getDbConnection());

		return array(
				'permissions' => array(self::HAS_MANY, 'YumPermission', 'principal_id'),
				'managed_by' => array(self::HAS_MANY, 'YumPermission', 'subordinate_id'),
				'messages' => array(self::HAS_MANY, 'YumMessage', 'to_user_id', 'order' => 'messages.id DESC'),
				'visits' => array(self::HAS_MANY, 'YumProfileVisit', 'visited_id'),
				'profile' => array(self::HAS_ONE, 'YumProfile', 'user_id' ),
				'friendships' => array(self::HAS_MANY, 'YumFriendship', 'inviter_id'),
				'friendships2' => array(self::HAS_MANY, 'YumFriendship', 'friend_id'),
				'friendship_requests' => array(self::HAS_MANY, 'YumFriendship', 'friend_id', 'condition' => 'status = 1'), // 1 = FRIENDSHIP_REQUEST
				'roles' => array(self::MANY_MANY, 'YumRole', $relationUHRTableName . '(user_id, role_id)'),
				'memberships' => array(self::HAS_MANY, 'YumMembership', 'user_id'),
				'privacy' => array(self::HAS_ONE, 'YumPrivacySetting', 'user_id'),
				);
	}

	public function isFriendOf($invited_id) {
		foreach ($this->getFriendships() as $friendship) {
			if ($friendship->inviter_id == $this->id && $friendship->friend_id == $invited_id)
				return $friendship->status;
		}

		return false;
	}

	public function getFriendships() {
		$condition = 'inviter_id = :uid or friend_id = :uid';
		return YumFriendship::model()->findAll($condition, array(
			':uid' => $this->id));
	}

	// Friends can not be retrieve via the relations() method because a friend
	// can either be in the invited_id or in the friend_id column.
	// set $everything to true to also return pending and rejected friendships
	public function getFriends($everything = false) {
		if ($everything)
			$condition = 'inviter_id = :uid';
		else
			$condition = 'inviter_id = :uid and status = 2';

		$friends = array();
		$friendships = YumFriendship::model()->findAll($condition, array(
					':uid' => $this->id));
		if ($friendships != NULL && !is_array($friendships))
			$friendships = array($friendships);

		if ($friendships)
			foreach ($friendships as $friendship)
				$friends[] = YumUser::model()->findByPk($friendship->friend_id);

		if ($everything)
			$condition = 'friend_id = :uid';
		else
			$condition = 'friend_id = :uid and status = 2';

		$friendships = YumFriendship::model()->findAll($condition, array(
					':uid' => $this->id));

		if ($friendships != NULL && !is_array($friendships))
			$friendships = array($friendships);


		if ($friendships)
			foreach ($friendships as $friendship)
				$friends[] = YumUser::model()->findByPk($friendship->inviter_id);

		return $friends;
	}

	public function register($username=null, $password=null, $email=null) {
		if ($username !== null && $password !== null) {
			// Password equality is checked in Registration Form
			$this->username = $username;
			$this->password = $this->encrypt($password);
		}

		foreach (YumProfile::model()->findAll() as $profile) {
			if ($email == $profile->email)
				return false;
		}

		$this->activationKey = $this->generateActivationKey(false, $password);
		$this->createtime = time();
		$this->superuser = 0;

		switch (Yum::module()->registrationType) {
			case YumRegistration::REG_SIMPLE:
				$this->status = YumUser::STATUS_ACTIVE;
				break;
			case YumRegistration::REG_NO_PASSWORD:
			case YumRegistration::REG_EMAIL_CONFIRMATION:
			case YumRegistration::REG_CONFIRMATION_BY_ADMIN:
				$this->status = YumUser::STATUS_NOTACTIVE;
				break;
			case YumRegistration::REG_NO_PASSWORD_ADMIN_CONFIRMATION:
			case YumRegistration::REG_EMAIL_AND_ADMIN_CONFIRMATION:
				// Users stay banned until they confirm their email address.
				$this->status = YumUser::STATUS_BANNED;
				break;
		}

		if(Yum::module()->enableRoles) 
			$this->roles = YumRole::getAutoassignRoles(); 

		return $this->save();
	}

	public function isPasswordExpired() {
		$distance = Yii::app()->getModule('user')->password_expiration_time * 60 * 60;
		return $this->lastpasswordchange - $distance > time();
	}

	/**
	 * Activation of an user account. 
	 */
	public function activate($email=null, $key=null) {
		// If everything is set properly,
		if ($email != null && $key != null) {
			// and the emails exists in the database,
			if($profile = YumProfile::model()->find("email = '{$email}'")) {
				// and is associated with a correct user,
				if($user = $profile->user) {	
					// and this user has the status NOTACTIVE 
					if ($user->status != self::STATUS_NOTACTIVE)
						return false;
					// and the given activationKey is identical to the one in the
					// database
					if ($user->activationKey == $key) {
						// then generate a new Activation key to avoid double activation, 
						// set the status to ACTIVATED and save the data
						$user->activationKey = $user->generateActivationKey(true);
						$user->status = self::STATUS_ACTIVATED;
						if($user->save(false, array('activationKey', 'status'))) {
							if(Yum::module()->enableActivationConfirmation) {
								YumMessage::write($user, 1,
										Yum::t('Your activation succeeded'),
										YumTextSettings::getText('text_email_activation', array(
												'{username}' => $user->username,
												'{link_login}' =>
												Yii::app()->controller->createUrl('//user/user/login'))));
							}

							return $user;
						}
					} 
				}
			}
		}
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
	public function generateActivationKey($activate=false, $password='', array $params=array()) {
		return $activate ? YumUser::encrypt(microtime()) : YumUser::encrypt(microtime() . $this->password);
	}

	public function attributeLabels() {
		return array(
				'id' => Yum::t('#'),
				'username' => Yum::t("Username"),
				'password' => Yum::t("Password"),
				'verifyPassword' => Yum::t("Retype password"),
				'verifyCode' => Yum::t("Verification code"),
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
	public static function encrypt($string = "") {
		$salt = Yum::module()->salt;
		$hashFunc = Yum::module()->hashFunc;
		$string = sprintf("%s%s%s", $salt, $string, $salt);

		if (!function_exists($hashFunc))
			throw new CException('Function `' . $hashFunc . '` is not a valid callback for hashing algorithm.');

		return $hashFunc($string);
	}

	public function scopes() {
		return array(
			'active' => array('condition' => 'status=' . self::STATUS_ACTIVE,),
			'activefirstvisit' => array('condition' => 'status=' . self::STATUS_ACTIVE_FIRST_VISIT,),
			'notactive' => array('condition' => 'status=' . self::STATUS_NOTACTIVE,),
			'banned' => array('condition' => 'status=' . self::STATUS_BANNED,),
			'superuser' => array('condition' => 'superuser=1',),
			'public' => array(
			'join' => 'LEFT JOIN privacysetting on t.id = privacysetting.user_id',
					'condition' => 'appear_in_search = 1',),
		);
	}

	public static function itemAlias($type, $code=NULL) {
		$_items = array(
			'NotifyType' => array(
				'None' => Yum::t('None'),
				'Digest' => Yum::t('Digest'),
				'Instant' => Yum::t('Instant'),
			),
			'UserStatus' => array(
				'0' => Yum::t('Not active'),
				'1' => Yum::t('Activated'),
				'2' => Yum::t('Active - First visit'),
				'3' => Yum::t('Active'),
				'-1' => Yum::t('Banned'),
				'-2' => Yum::t('Deleted'),
			),
			'AdminStatus' => array(
				'0' => Yum::t('No'),
				'1' => Yum::t('Yes'),
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
	public static function getUsersByRole($roleTitle) {
		$role = YumRole::model()->findByAttributes(array('title' => $roleTitle));
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

	public function getAvatar($thumb = false) {
		if (Yum::module()->enableAvatar) {
			$return = '<div class="avatar">';

			$options = array();
			if ($thumb)
				$options = array('style' => 'width: 50px; height:50px;');
			else
				$options = array('style' => 'width: '.Yum::module()->avatarDisplayWidth.'px;');

			if (isset($this->avatar) && $this->avatar)
				$return .= CHtml::image(Yii::app()->baseUrl . '/'
						. $this->avatar, 'Avatar', $options);
			else
				$return .= CHtml::image(Yii::app()->getAssetManager()->publish(
							Yii::getPathOfAlias('YumAssets.images') . ($thumb ? '/no_avatar_available_thumb.jpg' : '/no_avatar_available.jpg'),
							Yum::t('No image available'), array(
								'title' => Yum::t('No image available'))));
			$return .= '</div><!-- avatar -->';
			return $return;
		}
	}
}
