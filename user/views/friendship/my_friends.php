<div id="friends">
<h2> <?php echo Yum::t('My friends'); ?> </h2>
<?php
if($friends) {
	foreach($friends as $friend) {
			echo '<div id="friend">
			<div id="avatar">';
			$friend->getAvatar($friend, true);
		?>
			<div id='user'>
			<?php 
			echo CHtml::link(ucwords($friend->username), array(
						'//user/profile/view', 'id'=>$friend->id));
		?>
			</div>
			</div>
			</div>
			<?php
	}
}else {
	echo Yum::t('You have no friends yet');
}
?>
</div>

