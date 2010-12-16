<?php
if(Yii::app()->getModule('user')->rtepath != false)
  Yii::app()->clientScript-> registerScriptFile(Yii::app()->getModule('user')->rtepath);                                                                         
if(Yii::app()->getModule('user')->rteadapter != false)
  Yii::app()->clientScript-> registerScriptFile(Yii::app()->getModule('user')->rteadapter); 
?>

<?php 
$this->pageTitle=Yii::app()->name . ' - '.Yum::t( "Profile");
$this->breadcrumbs=array(
	Yum::t( "Profile")=>array('profile'),
	Yum::t( "Edit"));
$this->title = Yum::t( 'Edit profile');
?>

<?php if(Yii::app()->user->hasFlash('profileMessage')): ?>
<div class="success">
<?php echo Yii::app()->user->getFlash('profileMessage'); ?>
</div>
<?php endif; ?>
<div class="form">

<?php echo CHtml::beginForm(); ?>

<?php echo Yum::requiredFieldNote(); ?>

<?php echo CHtml::errorSummary($model);
  echo CHtml::errorSummary($profile); ?>
<?php 
$profileFields=YumProfileField::model()->forOwner()->sort()->with('group')->together()->findAll();

if ($profileFields) 
{
	foreach($profileFields as $field) 
	{
			?>
	<div class="row">
	<?php echo CHtml::activeLabelEx($profile,$field->varname);
			if ($field->field_type=="TEXT") {
				echo CHtml::activeTextArea($profile,
						$field->varname,
						array('rows'=>6, 'cols'=>50));
				if(Yii::app()->getModule('user')->rtepath != false)
					Yii::app()->clientScript->registerScript("ckeditor", "$('#YumProfile_".$field->varname."').ckeditor();"); 
			} 
			else if($field->field_type == "DROPDOWNLIST") {
			echo CHtml::activeDropDownList($profile,
					$field->varname, 
					CHtml::listData(CActiveRecord::model(ucfirst($field->varname))->findAll(),
						'id',
						$field->related_field_name));

			} else {
				echo CHtml::activeTextField($profile,
						$field->varname,
						array('size'=>60,'maxlength'=>(($field->field_size)?$field->field_size:255)));
			}
			echo CHtml::error($profile,$field->varname); ?>
	</div>	
			<?php
			}
		}
?>
		<?php if(Yum::module()->loginType & UserModule::LOGIN_BY_EMAIL): ?>
	<div class="row">
		<?php echo CHtml::activeLabelEx($model,'username'); ?>
		<?php echo CHtml::activeTextField($model,'username',array('size'=>20,'maxlength'=>20)); ?>
		<?php echo CHtml::error($model,'username'); ?>
	</div>
		<?php endif; ?>

		<?php if(Yii::app()->getModule('user')->notifyType == 'User'): ?>
	<div class="row">
		<?php echo CHtml::activeLabelEx($model,'notifyType'); ?>
		<?php echo CHtml::activeDropDownList($model,'notifyType',YumUser::itemAlias('NotifyType')); ?>
		<?php echo CHtml::error($model,'notifyType'); ?>
	</div>
		<?php endif; ?>



	<div class="row">
		<?php 
		echo CHtml::activeLabelEx($profile, 'privacy'); 
		echo CHtml::activeDropDownList($profile, 'privacy',
				array(
					'protected' => Yum::t( 'protected'),
					'private' => Yum::t( 'private'),
					'public' => Yum::t( 'public'),
					)
); 
				echo CHtml::error($profile,'privacy'); 
?>

		</div>

<div class="row">
		<?php 
		echo CHtml::activeLabelEx($profile, 'show_friends'); 
		echo CHtml::activeDropDownList($profile, 'show_friends',
				array(
					'0' => Yum::t( 'owner'),
					'1' => Yum::t( 'friends only'),
					'2' => Yum::t( 'public'),
					)
					);
		?>
</div>

<div class="row">
		<?php 
		echo CHtml::activeLabelEx($profile, 'allow_comments'); 
		echo CHtml::activeDropDownList($profile, 'allow_comments',
				array(
					'0' => Yum::t( 'No'),
					'1' => Yum::t( 'Yes'),
					)
					);
		?>
</div>



	<div class="row buttons">
		<?php echo CHtml::submitButton($model->isNewRecord ? Yii::t("UserModule.user", 'Create') : Yii::t("UserModule.user", 'Save')); ?>
	</div>

<?php echo CHtml::endForm(); ?>

</div><!-- form -->
