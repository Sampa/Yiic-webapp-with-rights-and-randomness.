<?php

class YumUser extends YumMongoRecord {
	const STATUS_NOTACTIVE = 0;
	const STATUS_ACTIVATED = 1;
	const STATUS_ACTIVE_FIRST_VISIT = 2;
	const STATUS_ACTIVE = 3;
	const STATUS_BANNED = -1;
	const STATUS_REMOVED = -2;

	public $username;
	public $password;
	public $activationKey;
	public $status;

	public function getCollectionName() {
		return Yum::module()->userCollectionName;
	}

	public function attributeLabels() {
		return array(
				'id' => Yum::t('#'),
				'username' => Yum::t("Username"),
				'password' => Yum::t("Password"),
				'activationKey' => Yum::t("Activation key"),
				'status' => Yum::t("Status"),
				);
	}

}
