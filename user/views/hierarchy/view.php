<?php
/// Helper function for generating menu entries
function f($text, $url = '#', $childs = array()) {
	return array('text' => CHtml::link(Yum::t($text), $url), 'children' => $childs);
}

if(!Yii::app()->user->isGuest) {
	$hierarchy = array();

	foreach(YumUser::model()->findAll('superuser = 0') as $user) {
		$childnodes = array();
		$childnodes_users = array();
		$childnodes_roles = array();

		foreach($user->users as $childuser)
			$childnodes_users[] = f($childuser->username, array('user/view', 'id' => $user->id));

		foreach($user->roles as $role)
			$childnodes_roles[] = f($role->title, array('role/view', 'id' => $role->id));

		if(count($childnodes_users) > 0)
			$childnodes[] = array('text' => 'Allowed Users', 'children' => $childnodes_users);
		else
			$childnodes[] = array('text' => 'User can not administer any users');

		if(count($childnodes_roles) > 0)
			$childnodes[] = array('text' => 'Allowed Roles', 'children' => $childnodes_roles);
		else
			$childnodes[] = array('text' => 'User can not administer any users of any role');

		$hierarchy[] = f($user->username, array('user/view', 'id' => $user->id), $childnodes);
	}

	echo '<div id="hierarchy">'; 	
	$this->widget('CTreeView', array(
				'data' => $hierarchy, 
				'options' => array('collapsed' => true),
				));

	echo '</div>';
}
?>
