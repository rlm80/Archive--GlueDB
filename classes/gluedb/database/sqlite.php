<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base Sqlite database class.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Database_Sqlite extends GlueDB_Database {
	/**
	 * @var string Absolute path to the database file on disk.
	 */
	protected $path;

	/*
	 * Builds DSN string ( http://www.php.net/manual/en/ref.pdo-sqlite.connection.php ).
	 *
	 * @returns string
	 */
	protected function dsn() {
		// Builds DSN :
		$dsn = 'sqlite:' . $this->path;

		// Unset connection parameters to make sure no forgotten debug message
		// displays them unintentionaly to a user :
		$this->path = null;

		return $dsn;
	}
}