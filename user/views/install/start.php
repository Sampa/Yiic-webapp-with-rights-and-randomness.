<H1> Yii User Management Module Installation </h1>

<?php echo CHtml::beginForm(array('install')); ?>

<div class="span-12">
	<h2> Information: </h2>
	<hr />
	<p> You are about to install the Yii User management Module version
<?php echo Yii::app()->getModule('user')->version; ?>
 in your Web Application. You require a working database connection to an mysql
Database. Other Databases are not supported at this time. Please make sure 
your Database is Accessible in protected/config/main.php. </p>

	<?php if (Yii::app()->db): ?>
	<div class="hint"> Your database connection seems to be working </div>
	<?php else: ?>
	<div class="error"> Your database connection doesn't seem to be working </div>
	<?php endif; ?>

	<p> <strong> Every existing Table will be deleted! If you are really sure you
want to do install the Yii-User Module, switch the 'debug' parameter to
true, run the installer and switch it back to false, so your data doesn't
get overriden accidentally. </strong> </p>

	<p> This Installer will create the needed database Tables in your Database and
some Demo Data. If you want so see, what will happen exactly, look at the 
controllers/InstallController.php file in the Module Source. </p>

	<p> The API Documentation, examples and an Database Schema for Mysql Workbench
can be found in the docs/ directory of the Module. Please also take a look
in this directory when you want to use the Role Manager of yii-user. </p>

	<p> To set the language of this Module, set the 'language' config param of
your Yii Web Application </p>

</div>

<div class="span-11 last">

	<h2> Configuration: </h2>
	<hr />
	<p> the Yii User Manager will generate the following Tables: </p>
	<table>
		<tr>
			<td> Table for Users </td>
			<td> <?php echo CHtml::textField('usersTable', $usersTable); ?> </td>
		</tr>

	</table>

	<div style="clear:both;"> </div>

	<label for="installRole"> Install Role Management submodule </label>
	<?php echo CHtml::checkBox('installRole', true); ?>

	<div id="roles">
		<table>
			<tr>
				<td> Table for the Roles </td>
				<td> <?php echo CHtml::textField('rolesTable', $rolesTable); ?> </td> 
			</tr>
			<tr>
				<td> Table for the Role Assignment </td>
				<td> <?php echo CHtml::textField('userRoleTable', $userRoleTable); ?> </td>
			</tr>
			<tr>
				<td> This table holds the assignment, which user can administrate which other users </td>
				<td> <?php echo CHtml::textField('userUserTable', $userUserTable); ?> </td>
				</tr>
				<tr>
				<td> This table holds the assignment, which roles can administrate which other roles </td>
				<td> <?php echo CHtml::textField('roleRoleTable', $roleRoleTable); ?> </td>
				</tr>
				</table>
				</div>

	<div style="clear:both;"> </div>

	<label for="installMessages"> Install Messages submodule </label>
	<?php echo CHtml::checkBox('installMessages', true); ?>
	<div id="messages">
		<table>
			<tr>
				<td> Table for Messages </td>
				<td> <?php echo CHtml::textField('messagesTable', $messagesTable); ?> </td> 
			</tr>
		</table>
	</div>

	<div style="clear:both;"> </div>

	<label for="installRole"> Install Profiles submodule </label>
	<?php echo CHtml::checkBox('installProfiles', true); ?>

	<div id="profiles">
		<table>
			<tr> 
				<td> Table for the Profile Fields </td>
				<td> <?php echo CHtml::textField('profileFieldsTable', $profileFieldsTable);?> </td> 
			</tr>
			<tr> 
				<td> Table for the Profile Fields Group</td>
				<td> <?php echo CHtml::textField('profileFieldsGroupTable', $profileFieldsGroupTable);?> </td> 
			</tr>			
			<tr> 
				<td> Table for the Profile Visits</td>
				<td> <?php echo CHtml::textField('profileVisitTable', $profileVisitTable);?> </td> 
			</tr>			

			<tr> 
				<td> Table for the Profiles </td>
				<td> <?php echo CHtml::textField('profileTable', $profileTable); ?> </td> 
			</tr>
		</table>
	</div>

	<div style="clear:both;"> </div>

	<label for="installSettingsTable"> Enable Module settings Configuration by Database Table </label>
	<?php echo CHtml::checkBox('installSettingsTable', true); ?>
	<div id="settings">
		<table>
			<tr>
				<td> Table for User Management Settings </td>
				<td> <?php echo CHtml::textField('settingsTable', $settingsTable); ?> </td>
			</tr>
			<tr>
				<td> Table for User Management Text Settings </td>
				<td> <?php echo CHtml::textField('textSettingsTable', $textSettingsTable); ?> </td>
			</tr>
		</table>
	</div>

	<div style="clear:both;"> </div>

	<label for="installDemoData"> Install Demo data (recommended) ? </label>
	<?php echo CHtml::checkBox('installDemoData', $this->module->installDemoData); ?>

</div>

	<?php 
	$js = "
	$('#installSettingsTable').click(function() {
	$('#settings').toggle();
	});
	$('#installRole').click(function() {
	$('#roles').toggle();
	});
	$('#installMessages').click(function() {
	$('#messages').toggle();
	});
	$('#installProfiles').click(function() {
	$('#profiles').toggle();
	});
	
	";
Yii::app()->clientScript->registerScript('install', $js); ?>

<?php echo CHtml::submitButton(Yii::t('UserModule.user', 'Install module')); ?>
<?php echo CHtml::endForm(); ?>

