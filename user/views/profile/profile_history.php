<?php
	printf('<h2>%s</h2>', Yii::t('UserModule.user', 'Profile history'));

	$cmp_attributes = YumProfileField::model()->findAll();

	if(!is_array($model->profile))
		$model->profile = array($model->profile);

	$i = 0;
	foreach($model->profile as $profile) {
		$data = $model->profile[$i]->compare(
				isset($model->profile[$i + 1]) 
				? $model->profile[$i + 1] : $model->profile[$i]);
		$i++;
		printf ('<h3> %s: %s (%s) </h3>',
				Yii::t('UserModule.user', 'Profile number'),
				$profile->profile_id,
				$profile->timestamp
//				CHtml::link(Yii::t('UserModule.user', 'Open profile'), array(
//						'//user/profile/view', 'id' => $profile->profile_id))
				);

		printf('<table><tr><th>%s</th><th>%s</th><th>%s</th></tr>', 
				Yii::t('UserModule.user', 'Field'),
				Yii::t('UserModule.user', 'Old value'),
				Yii::t('UserModule.user', 'New value')) ;
		$count = 0;
		foreach($cmp_attributes as $field) {
			if(isset($data[$field->varname])) {
				$count++;
				printf('<tr> <td> %s </td> <td> %s </td> <td> %s <td> </tr>',
						Yii::t('app', $field->varname),
						$data[$field->varname]['new'],
						$data[$field->varname]['old']); 
			}
		}
			if($count == 0)
				printf('<tr> <td colspan=3> %s </td> </tr>', Yii::t('UserModule.user', 'No profile changes were made')); 

		printf('</table>');
	}
echo '<br />';
?>
