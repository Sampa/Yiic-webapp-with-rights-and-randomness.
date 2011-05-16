<h2> <?php echo Yum::t('Activation did not work'); ?> </h2>

<?php if($error == -1) echo Yum::t('The user is already activated'); ?>
<?php if($error == -2) echo Yum::t('Wrong activation Key'); ?>
