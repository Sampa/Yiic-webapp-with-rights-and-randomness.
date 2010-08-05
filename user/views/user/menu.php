<?php
// Draw menu only when logged in into the System
if(!Yii::app()->user->isGuest) {


$menu = array();
// Gather available menu entries
if(Yii::app()->user->isAdmin()) {
	$usermenu = array();
	$rolemenu = array();
	$profilemenu = array();
	$settingsmenu = array();
	$other = array();

	$usermenu[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'User administration panel'), array('user/adminpanel')));
	$usermenu[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'Show users'), array('user/admin')));
	$usermenu[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'Create new user'), array('user/create')));

	$rolemenu[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'Show roles'), array('role/admin')));
	$rolemenu[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'Create new role'), array('role/create')));

	$profilesettings[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'Manage profile fields'), array('fields/admin')));
	$profilesettings[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'Create profile field'), array('fields/create')));

	$profilegroupsettings[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'Manage field groups'), array('fieldsgroup/admin')));
	$profilegroupsettings[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'Create new field group'), array('fieldsgroup/create')));


	$profilemenu[] = array('children' => $profilesettings, 'text' => Yii::t('UserModule.user', 'Manage profile fields'));
	$profilemenu[] = array('children' => $profilegroupsettings, 'text' => Yii::t('UserModule.user', 'Manage profile field groups'));

	$settingsmenu[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'Module settings'), array('yumSettings/index')));
	$settingsmenu[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'Module text settings'), array('yumTextSettings/admin')));

	$messagesmenu[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'View admin messages'), array('messages/index')));
	$messagesmenu[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'Write a message'), array('messages/compose')));
	$othermenu[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'Change admin Password'), array('user/changePassword')));
	$othermenu[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'Logout'), array('user/logout')));

	$menu[] = array('children' => $usermenu, 'text' => Yii::t('UserModule.user', 'User Administration'));	
	$menu[] = array('children' => $rolemenu, 'text' => Yii::t('UserModule.user', 'Role Administration'));	
	$menu[] = array('children' => $profilemenu, 'text' => Yii::t('UserModule.user', 'Profile fields'));	
	$menu[] = array('children' => $messagesmenu, 'text' => Yii::t('UserModule.user', 'Messages')); 
	$menu[] = array('children' => $settingsmenu, 'text' => Yii::t('UserModule.user', 'Settings')); 
	$menu[] = array('children' => $othermenu, 'text' => Yii::t('UserModule.user', 'Other')); 
} else if(!Yii::app()->user->isguest) {

	if(Yii::app()->user->hasUsers())
		$menu[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'Manage my users'), array('user/admin')));
	$menu[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'View users'), array('user/index')));
	$menu[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'My Inbox'), array('messages/index')));
	$menu[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'Write a message'), array('messages/compose')));
	$menu[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'Change password'), array('user/changePassword')));
	$menu[] = array('text' => CHtml::link(Yii::t('UserModule.user', 'Delete account'), array('user/delete')));
}
echo '<div class="yum_menu" style="float:right; width:25%; margin: 0px 5px 0px 5px;">';
$this->beginWidget('zii.widgets.CPortlet', array(
			'title'=>Yii::t('UserModule.user', 'User Operations' )));
$this->widget('CTreeView', array(
'data' => $menu, 
));
$this->endWidget();

echo '</div>';

}
?>
