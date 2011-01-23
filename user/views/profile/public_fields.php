<?php foreach($profile->getPublicFields() as $field) { ?>

	<div class="row">
	<p class="profilefieldlabel"> 
		<strong> <?php echo Yum::t($field->title); ?> </strong> </p> 
	<p> <?php echo $profile->{$field->varname}; ?> </p>
	</div>

<?php } ?>

<div class="clear"></div>
