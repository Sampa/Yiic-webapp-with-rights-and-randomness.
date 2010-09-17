<?php

class YumProfileController extends YumController
{
	const PAGE_SIZE=10;
	private $_model;

	public function accessRules()
	{
		return array(
			array('allow', 
				'actions'=>array('index', 'create', 'admin','delete', 'visits'),
				'expression' => 'Yii::app()->user->isAdmin()'
				),
			array('allow', 
				'actions'=>array('view', 'update'),
				'users' => array('@'),
				),

			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	public function actionVisits() {
		$this->layout = YumWebModule::yum()->adminLayout;

		$this->render('visits',array(
			'model'=>new YumProfileVisit(),
		));

	}

	public function actionView() {
		$this->layout = Yii::app()->getModule('user')->profileLayout;
		$view = Yii::app()->getModule('user')->profileView;

		$model = YumUser::model()->findByPk($_GET['id']);

		$this->render($view, array('model' => $model));
		$this->updateVisitor(Yii::app()->user->id, $model->id);
	}

	public function updateVisitor($visitor_id, $visited_id) {
		$visit = YumProfileVisit::model()->find('visitor_id = :visitor_id and visited_id = :visited_id', array(
					':visitor_id' => $visitor_id,
					':visited_id' => $visited_id));
		if($visit) {
			$visit->save();	
		} else {
			$visit = new YumProfileVisit();
			$visit->visitor_id = $visitor_id;
			$visit->visited_id = $visited_id;
			$visit->save();
			print_r($visit->getErrors());
		}
	}

	public function actionCreate()
	{
		$this->layout = YumWebModule::yum()->adminLayout;
		$model = new YumProfile;

		if(isset($_POST['YumProfile']))
		{
			$model->attributes=$_POST['YumProfile'];

			if($model->validate()) 
			{
				$model->save();
				$this->redirect(array('admin'));
			}
		}

		$this->render('create',array( 'model'=>$model ));
	}

	public function actionUpdate()
	{
		if(Yii::app()->user->isAdmin()) 
			$this->layout = YumWebModule::yum()->adminLayout;
		else
			$this->layout = YumWebModule::yum()->layout;

		if(!isset($_GET['id'])) {
			$profile = YumProfile::model()->find('user_id = ' . Yii::app()->user->id);
			$_GET['id'] = $profile->profile_id;
		}

		$model=$this->loadModel();
		if(isset($_POST['YumProfile']))
		{
			$model->attributes=$_POST['YumProfile'];
			
			if($model->save())
				$this->redirect(array('admin'));
		}

		$this->render('/user/update',array(
'model'=>$model->user,
'profile' => $model,
'passwordform' => new YumUserChangePassword(),
 ));
	}

	public function actionDelete()
	{
		$this->layout = YumWebModule::yum()->adminLayout;

		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$model = $this->loadModel();
			$model->delete();

			if(!isset($_POST['ajax']))
				$this->redirect(array('admin'));
		}
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}

	public function actionIndex()
	{
		$this->actionAdmin();
	}

	public function actionAdmin()
	{
		$this->layout = YumWebModule::yum()->adminLayout;

		$dataProvider=new CActiveDataProvider('YumProfile', array(
			'pagination'=>array(
				'pageSize'=>self::PAGE_SIZE,
			),
			'sort'=>array(
				'defaultOrder'=>'profile_id',
			),
		));

		$this->render('admin',array(
			'dataProvider'=>$dataProvider,
		));
	}

	/**
	 * @return YumProfileField
	 */
	public function loadModel()
	{
		if($this->_model===null)
		{
			if(isset($_GET['id']))
				$this->_model=YumProfile::model()->findbyPk($_GET['id']);
			if($this->_model===null)
				throw new CHttpException(404,'The requested page does not exist.');
		}
		return $this->_model;
	}
}
