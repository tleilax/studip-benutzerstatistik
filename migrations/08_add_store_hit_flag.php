<?php
class AddStoreHitFlag extends DBMigration
{
	function up()
	{
		$config = new Config();
		$config->setValue('1', 'BENUTZERSTATISTIK_STORE_HITS');
	}

	function down()
	{
		$config = new Config();
		$config->unsetValue('BENUTZERSTATISTIK_STORE_HITS');
	}
}