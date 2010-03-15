<?php

class WebUser extends CWebUser
{
	public $loginUrl=array('/user/user/login');

	/**
	 * Return admin status.
	 * @return boolean
	 */
	public function isAdmin() {
		if($this->isGuest)
			return false;
		else {
			if(User::model()->active()->superuser()->findbyPk(Yii::app()->user->id))
				return true;
			else
				return false;
		}
	}
}
?>
