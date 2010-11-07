<?php
$friends = $user->getFriends();
if($friends) {
	echo '<ul>';
	foreach($friends as $friend) {
		printf('<li>%s</li>', $friend->username);	
	}
	echo '</ul>';
} else
printf('<p>%s</p>', Yum::t('This user has no friends yet.'));
?>
