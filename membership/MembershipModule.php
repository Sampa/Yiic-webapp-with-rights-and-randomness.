<?php
Yii::setPathOfAlias('MembershipModule' , dirname(__FILE__));

class MembershipModule extends CWebModule {
	public $membershipExpiredView = '/membership/membership_expired';

	public $controllerMap=array(
			'payment'=>array(
				'class'=>'MembershipModule.controllers.YumPaymentController'),
			'membership'=>array(
				'class'=>'MembershipModule.controllers.YumMembershipController'),
			);

	public function beforeControllerAction($controller, $action) {
		if(!Yum::hasModule('role'))
			throw new Exception(
					'Using the membership submodule requires the role module activated');

		return parent::beforeControllerAction($controller, $action);
	}
}
