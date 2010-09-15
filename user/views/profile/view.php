<?php
$this->pageTitle=Yii::app()->name . ' - ' . Yum::t('Profile');
$this->breadcrumbs=array(Yum::t('Profile'));
$this->title = Yum::t('Profile');
?>

<table class="dataGrid">
<tr>
	<th class="label"><?php echo CHtml::encode($model->user->username); ?>
</th>
    <td><?php echo CHtml::encode($model->user->username); ?>
</td>
</tr>
<?php 
		$profileFields = YumProfileField::model()->forOwner()->sort()->with('group')->together()->findAll();
		if ($profileFields) {
			foreach($profileFields as $field) {
				?>
					<tr>
					<th class="label"><?php echo CHtml::encode(Yum::t($field->title)); ?>
					</th>
					<td><?php echo CHtml::encode($model->getAttribute($field->varname)); ?>
					</td>
					</tr>
					<?php
			}
		}
?>
<tr>
<th class="label"><?php echo Yum::t('first visit'); ?>
</th>
<td><?php echo date(UserModule::$dateFormat,$model->user->createtime); ?>
</td>
</tr>
<tr>
	<th class="label"><?php echo Yum::t('last visit'); ?>
</th>
    <td><?php echo date(UserModule::$dateFormat,$model->user->lastvisit); ?>
</td>
</tr>
</table>
