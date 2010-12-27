<?php

Yii::import('application.modules.user.controllers.YumController');

class YumMessagesController extends YumController
{
	private $_model;
	
	public function accessRules()
	{
		return array(
			array('allow',
				'actions' => array('view', 'compose', 'index',
					'delete', 'sent', 'success', 'users'),
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

	public function actionView() {
		$model = $this->loadModel();

		if($model->to_user_id != Yii::app()->user->id
				&& $model->from_user_id != Yii::app()->user->id) {
			$this->render('message_view_forbidden');
		} else {

			if(!$model->message_read) {
				$model->message_read = true;
				$model->save();
			}

			$this->render('view',array('model'=>$model));
		}
	}

	public function actionCompose() {
		$model = new YumMessage;

		$this->performAjaxValidation($model);

		if(isset($_POST['YumMessage'])) {			
			$model = new YumMessage;
			$model->attributes=$_POST['YumMessage'];
			$model->from_user_id = Yii::app()->user->id;

			if($model->validate()) {
				foreach($_POST['YumMessage']['to_user_id'] as $user_id) {
					$model = new YumMessage;
					$model->attributes = $_POST['YumMessage'];
					$model->from_user_id = Yii::app()->user->id;
					$model->to_user_id = $user_id;
					$model->save();
				}
				$this->redirect(array('success'));
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
			$this->loadModel()->delete();
			if(!isset($_POST['ajax']))
				$this->redirect(array('index'));
	}

	public function actionIndex()
	{
		$this->render('index',array(
					'dataProvider'=>new CActiveDataProvider('YumMessage', array(
							'criteria' => array(
								'condition' => 'to_user_id = '. Yii::app()->user->id)))));
	}

	public function actionSent()
	{
		$this->render('sent',array(
					'dataProvider'=>new CActiveDataProvider('YumMessage', array(
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
					$recipients[] = $message->to_user->profile[0]->email;
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



	/**
	 * @return YumMessage
	 */
	public function loadModel()
	{
		if($this->_model===null)
		{
			if(isset($_GET['id']))
				$this->_model=YumMessage::model()->findbyPk($_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,Yii::t('App', 'The requested page does not exist.'));
		}
		return $this->_model;
	}

	protected function performAjaxValidation($model) {
		if(isset($_POST['ajax']) && $_POST['ajax']==='yum-messages-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}
