<?php
class AddIndices extends DBMigration
{
	function up()
	{
		DBManager::get()->exec("ALTER TABLE user_statistics ADD INDEX hits(hits), ADD INDEX javascript(javascript)");
	}

	function down()
	{
/*
		DBManager::get()->exec("ALTER TABLE user_statistics DROP INDEX hits, DROP INDEX javascript");
*/
	}
}