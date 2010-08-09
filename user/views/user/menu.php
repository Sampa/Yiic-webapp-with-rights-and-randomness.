<?php
// Helper function for generating menu entries
function e($text, $url) {
	return array('text' => sprintf('<span %s>%s</span>',
				strpos(Yii::app()->request->url, $url) === false ? '' : 'style="font-weight:bold;"',
				CHtml::link(Yum::t($text), array($url))));
}

// Draw menu only when logged in into the System

$module = Yii::app()->getModule('user');
if(!Yii::app()->user->isGuest) {
	$menu = array();
	// Gather available menu entries
	if(Yii::app()->user->isAdmin()) {
		$usermenu = array();
		$rolemenu = array();
		$profilemenu = array();
		$settingsmenu = array();
		$other = array();

		$usermenu[] = e('User administration Panel', 'user/adminpanel');
		$usermenu[] = e('Show users', 'user/admin');
		$usermenu[] = e('Create new user', 'user/create');

		$rolemenu[] = e('Show roles' ,'role/admin');
		$rolemenu[] = e('Create new role', 'role/create');

		$profilesettings[] = e('Manage profile fields', 'fields/admin');
		$profilesettings[] = e('Create profile field', 'fields/create');

		$profilegroupsettings[] = e('Manage field groups', 'fieldsgroup/admin');
		$profilegroupsettings[] = e('Create new field group', 'fieldsgroup/create');

		$profilemenu[] = array('children' => $profilesettings, 'text' => Yum::t('Manage profile fields'));
		$profilemenu[] = array('children' => $profilegroupsettings, 'text' => Yum::t('Manage profile field groups'));

		$settingsmenu[] = e('Edit module settings', 'yumSettings/update');
		$settingsmenu[] = e('Settings Profiles', 'yumSettings/index');
		$settingsmenu[] = e('Module text settings', 'yumTextSettings/admin');

		$messagesmenu[] = e('Admin inbox', 'messages/index');
		$messagesmenu[] = e('Admin sent messages', 'messages/sent');
		$messagesmenu[] = e('Write a message', 'messages/compose');
		$othermenu[] = e('Change admin Password', 'user/changePassword');
		$othermenu[] = e('Logout', 'user/logout');

		$menu[] = array('children' => $usermenu, 'text' => Yum::t('User Administration'));	

		if($module->enableRoles)
			$menu[] = array('children' => $rolemenu, 'text' => Yum::t('Role Administration'));	
		if($module->enableProfiles) 
			$menu[] = array('children' => $profilemenu, 'text' => Yum::t('Profile fields'));	
		if($module->messageSystem != YumMessage::MSG_NONE) 
			$menu[] = array('children' => $messagesmenu, 'text' => Yum::t('Messages')); 

		$menu[] = array('children' => $settingsmenu, 'text' => Yum::t('Settings')); 
		$menu[] = array('children' => $othermenu, 'text' => Yum::t('Other')); 
	} else if(!Yii::app()->user->isguest) {

		if(Yii::app()->user->hasUsers())
			$menu[] = e('Manage my users', 'user/admin');

		$messagesmenu[] = e('My Inbox', 'messages/index');
		$messagesmenu[] = e('Sent messages', 'messages/sent');
		$messagesmenu[] = e('Write a message', 'messages/compose');

		$menu[] = e('View users', 'user/index');

		if($module->messageSystem != YumMessage::MSG_NONE) 
			$menu[] = array('children' => $messagesmenu, 'text' => Yum::t('Messages')); 
		$menu[] = e('Change password', 'user/changePassword');
		$menu[] = e('Delete account', 'user/delete');
		$menu[] = e('Logout', 'user/logout');
	}
	echo '<div class="yum_menu" style="float:right; width:25%; margin: 0px 5px 0px 5px;">';
	$this->beginWidget('zii.widgets.CPortlet', array(
				'title'=>Yum::t('User Operations')));
	$this->widget('CTreeView', array(
				'data' => $menu, 
				));
	$this->endWidget();

	echo '</div>';

}
?>
