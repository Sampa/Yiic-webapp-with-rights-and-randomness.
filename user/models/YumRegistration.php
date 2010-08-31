<?php

class YumRegistration extends YumActiveRecord {
	const REG_DISABLED = 0;
	const REG_SIMPLE = 1;
	const REG_EMAIL_CONFIRMATION = 2;
	const REG_CONFIRMATION_BY_ADMIN = 3;
	const REG_EMAIL_AND_ADMIN_CONFIRMATION = 4;
}

?>
