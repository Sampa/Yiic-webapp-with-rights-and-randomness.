<?php

class WebUser extends CWebUser
{
	public $loginUrl=array('/user/user/login');


	/**
	 * Performs access check for this user.
	 * @param string the name of the role that need access check.
	 * @param array name-value pairs that would be passed to business rules associated
	 * with the tasks and roles assigned to the user.
	 * @param boolean whether to allow caching the result of access checki.
	 * This parameter has been available since version 1.0.5. When this parameter
	 * is true (default), if the access check of an operation was performed before,
	 * its result will be directly returned when calling this method to check the same operation.
	 * If this parameter is false, this method will always call {@link CAuthManager::checkAccess}
	 * to obtain the up-to-date access result. Note that this caching is effective
	 * only within the same request.
	 * @return boolean whether the operations can be performed by this user.
	 */
	public function checkAccess($operation,$params=array(),$allowCaching=true)
	{
		return $this->hasRole($operation);	
	}

	/**
	* Checks if the user has the given Role)
	* @mixed Role string or array of strings that should be checked
	* @int (optional) id of the user that should be checked 
	* @return bool Return value tells if the User has access or hasn't access.
	*/
	public static function hasRole($role, $uid = 0)
	{
		if($uid == 0)
			$uid = Yii::app()->user->getId();

		if(!is_array($role))
			$role = array ($role);

		$user = CActiveRecord::model('User')->findByPk($uid);
		if(isset($user->roles)) 
			foreach($user->roles as $roleobj) 
			{
				if(in_array($roleobj->title, $role) ||
				  in_array($roleobj->id, $role))
					return true;
			}
		return false;
	}




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
