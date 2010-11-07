<?php
$this->title = Yum::t('Manage friends');
$this->breadcrumbs = array('Friends', 'Admin');

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
?>

