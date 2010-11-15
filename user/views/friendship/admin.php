<?php
$this->title = Yum::t('Manage friends');
$this->breadcrumbs = array('Friends', 'Admin');


$this->widget('zii.widgets.grid.CGridView', array(
	'dataProvider'=>$model->search(),
	'filter' => $model,
		'columns'=>array(
			array(
				'name'=>'id',
				'filter' => false,
				'type'=>'raw',
				'value'=>'CHtml::link(CHtml::encode($data->id),
				array(Yum::route("{user}/update"),"id"=>$data->id))',
			),
			array(
				'name'=>'username',
				'visible' => Yum::module()->loginType != 'LOGIN_BY_EMAIL' ,
				'type'=>'raw',
				'value'=>'CHtml::link(CHtml::encode($data->username),
				array(Yum::route("friendship/friendadmin"),"id"=>$data->id))',
			),
			array(
				'header'=>Yum::t('First name'),
				'visible' => Yum::module()->enableProfiles,
				'type'=>'raw',
				'value'=>';CHtml::link(CHtml::encode($data->profile[0]->firstname),
				array(Yum::route("{user}/view"),"id"=>$data->id))',
			),
			array(
				'header'=>Yum::t('Last name'),
				'visible' => Yum::module()->enableProfiles,
				'type'=>'raw',
				'value'=>';CHtml::link(CHtml::encode($data->profile[0]->lastname),
				array(Yum::route("{user}/view"),"id"=>$data->id))',
			),
			array(
				'header'=>Yum::t('Email'),
				'visible' => Yum::module()->enableProfiles,
				'type'=>'raw',
				'value'=>';CHtml::link($data->profile[0]->email,
					\'mailto: \'.$data->profile[0]->email)'), 
			array(
				'name'=>'createtime',
				'filter' => false,
				'value'=>'date(UserModule::$dateFormat,$data->createtime)',
			),
			array(
				'name'=>'lastvisit',
				'filter' => false,
				'value'=>'date(UserModule::$dateFormat,$data->lastvisit)',
			),
			array(
				'name'=>'status',
				'filter' => false,
				'value'=>'YumUser::itemAlias("UserStatus",$data->status)',
			),
			array(
				'name'=>Yum::t('Roles'),
				'type' => 'raw',
				'filter' => false,
				'value'=>'$data->getRoles()',
			), 
			
))); 

/*
if($friendships) {
	echo '<table>';
	echo '<th>Username</th><th>Status</th>';
	foreach($friendships as $friendship) {
		printf('<tr><td>%s</td><td>%s</td>',
				$friendship->getFriend(),
				$friendship->getStatus());
	}
	echo '</table>';
} else {
	echo Yum::t('You do not have any friends yet');
}
*/
?>

