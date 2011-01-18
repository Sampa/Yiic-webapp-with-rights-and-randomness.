<?php
echo Yum::t('This users have visited your profile recently') . ': <br />';

	if($visits) {
		foreach($visits as $visit)
			printf('<div style="float:left">%s %s</div>', 
					CHtml::link($visit->visitor->getAvatar(true), array(
							'//user/profile/view', 'id' => $visit->visitor_id)),
						CHtml::link($visit->visitor->username, array(
								'//user/profile/view', 'id' => $visit->visitor_id)));
					} else
					echo Yum::t('Nobody has visited your profile yet');
					?>

<div style="clear: both;"> </div>
