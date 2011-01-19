<?php
$profileFields = YumProfileField::model()->findAll();
if ($profileFields) {
	echo '<table class="table_profile_fields">';
	foreach($profileFields as $field) {
		if($field->isPublic($profile->user->id))
			if($field->field_type == 'DROPDOWNLIST') {
				?>
					<tr>
					<th class="label"><?php echo CHtml::encode(Yum::t($field->title)); ?>
					</th>
					<td><?php
					echo CHtml::encode($profile->{ucfirst($field->varname)}->{$field->related_field_name}); ?>
					</td>
					</tr>
					<?php
			} else {
				?>
					<tr>
					<th class="label"><?php echo CHtml::encode(Yum::t($field->title)); ?>
					</th>
					<td><?php 
						echo CHtml::encode($profile->{$field->varname}); ?>
				</td>
				</tr>
							<?php
			}
	}
 echo '</table>';
}

?>
<div style="clear: both;"></div>
