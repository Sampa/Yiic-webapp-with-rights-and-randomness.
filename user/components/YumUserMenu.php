<?php
Yii::import('zii.widgets.CPortlet');

class YumUserMenu extends CPortlet {
	public function init() {
		$this->title = sprintf('%s <br /> %s: %s',
				Yum::t('Usermenu'),
				Yum::t('Logged in as'),
				Yii::app()->user->data()->username);

		$this->contentCssClass = 'menucontent';
		return parent::init();
	}

	public function run() {
		$this->widget('YumMenu', array(
					'items' => $this->getMenuItems()
					));

		parent::run();
	}

	public function getMenuItems() {
		return array( 
				array('label' => 'Profile', 'items' => array(
						array('label' => 'My Profile', 'url' => array('/user/profile/view')),
						array('label' => 'Edit personal data', 'url' => array('/user/profile/edit')),
						array('label' => 'Upload avatar image', 'url' => array('/user/avatar/editAvatar')),
						array('label' => 'Privacy settings', 'url' => array('/user/privacy/update')),
						)
					),

				array('label' => 'Membership', 'visible' => Yum::module()->enableMembership, 'items' => array(
						array('label' => 'My memberships', 'url' => array('/user/membership/index')),
						array('label' => 'Browse memberships', 'url' => array('/user/membership/order')),
						)
					),

				array('label' => 'Messages', 'items' => array ( 
						array('label' => 'My inbox', 'url' => array('/user/messages/index')),
						array('label' => 'Sent messages', 'url' => array('/user/messages/sent')),
						array('label' => 'Write a message', 'url' => array('/user/messages/compose')),
						),
					),

				array('label' => 'Social', 'items' => array(
							array('label' => 'My friends', 'url' => array('/user/friendship/index')),
							array('label' => 'Browse users', 'url' => array('/user/user/index')),
							array('label' => 'My groups', 'url' => array('/user/groups/index')),
							array('label' => 'Browse usergroups', 'url' => array('/user/groups/browse')),
							)
						),
				array('label' => 'Misc', 'items' => array(
							array('label' => 'Change password', 'url' => array('//user/user/changePassword')),
							array('label' => 'Delete account', 'url' => array('//user/user/delete')),
							array('label' => 'Logout', 'url' => array('//user/user/logout')),
							)
						),

				);
	}
}
?>






