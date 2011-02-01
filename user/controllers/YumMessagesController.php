<?php

Yii::import('application.modules.user.controllers.YumController');

class YumMessagesController extends YumController {
	public function accessRules() {
		return array(
			array('allow',
				'actions' => array('view', 'compose', 'index',
					'delete', 'sent', 'success', 'users', 'markRead'),
				'users'=>array('@'),
				),
			array('allow',
				'actions' => array('sendDigest'),
				'users'=>array('admin'),
				),
			array('deny',
				'users'=>array('*')
			)
		);
	}	

	public function actionUsers() {
		if(Yii::app()->request->isAjaxRequest)
			echo json_encode(CHtml::listData(YumUser::model()->findAll(), 'id', 'username'));
	}

	public function actionMarkRead() {
		$model = $this->loadModel('YumMessage');
		$model->message_read = true;
		$model->save();
		Yum::setFlash(Yum::t('Message "{message}" was marked as read', array(
					'{message}' => $model->title
					)));
		$this->redirect(array('//user/profile/view', 'id' => $model->from_user_id));
	}

	public function actionView() {
		$model = $this->loadModel('YumMessage');

		if($model->to_user_id != Yii::app()->user->id
				&& $model->from_user_id != Yii::app()->user->id) {
			$this->render('message_view_forbidden');
		} else {
			if(!$model->message_read) {
				$model->message_read = true;
				$model->save(false, array('message_read'));
			}

			$this->render('view',array('model'=>$model));
		}
	}

	public function actionCompose() {
		if(!Yii::app()->user->isAdmin() 
				&& !Yii::app()->user->data()->can('writeMessages')) {
			$this->render(Yum::module()->membershipExpiredView);
			Yii::app()->end();
		}
		$model = new YumMessage;

		if(isset($_POST['YumMessage'])) {			
			$model->attributes = $_POST['YumMessage'];
			$model->from_user_id = Yii::app()->user->id;

			if($model->save()) {
				Yum::setFlash(Yum::t('Message "{message}" has been sent to {to}', array(
								'{message}' => $model->title,
								'{to}' => YumUser::model()->findByPk($model->to_user_id)->username
								)));

				$this->redirect(array('index'));
			}
		}

		$this->render('compose',array(
			'model'=>$model,
			'to_user_id' => isset($_GET['to_user_id']) ? $_GET['to_user_id'] : false,
		));
	}

	public function actionSuccess() {
		$this->renderPartial('success');
	}

	public function actionDelete() {
			$this->loadModel('YumMessage')->delete();
			if(!isset($_POST['ajax']))
				$this->redirect(array('index'));
	}

	public function actionIndex()
	{
		$this->render('index',array(
					'dataProvider'=>new CActiveDataProvider('YumMessage', array(
							'pagination' => array(
								'pageSize' => 20,
								),
							'criteria' => array(
								'order' => 'timestamp DESC',
								'condition' => 'to_user_id = '. Yii::app()->user->id)))));
	}

	public function actionSent()
	{
		$this->render('sent',array(
					'dataProvider'=>new CActiveDataProvider('YumMessage', array(
							'pagination' => array(
								'pageSize' => 20,
								),

							'criteria' => array(
								'condition' => 'from_user_id = '. Yii::app()->user->id)))));
	}

	public function actionSendDigest() {
		$message = '';

		$recipients = array();
		if(isset($_POST['sendDigest'])) {
			foreach(YumMessage::model()->with('to_user')->findAll('not message_read') as $message) {
				if((is_object($message->to_user) && $message->to_user->notifyType == 'Digest')
						|| Yum::module()->notifyType == 'Digest') { 
					$this->mailMessage($message);
					$recipients[] = $message->to_user->profile->email;
				}
			}
			if(count($recipients) == 0)
				$message = Yum::t('No messages are pending. No message has been sent.'); 
			else {
				$message = Yum::t('Digest has been sent to {users} users:', array('{users}' => count($recipients)));
				$message .= '<ul>';
				foreach($recipients as $recipient) {
					$message .= sprintf('<li> %s </li>', $recipient);
				}
				$message .= '</ul>';
			}
		}
		$this->render('send_digest', array('message' => $message));
	}
}
