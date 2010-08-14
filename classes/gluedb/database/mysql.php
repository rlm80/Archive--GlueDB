<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base MySQL database class.
 *
 * @package    GlueDB
 * @author     R�gis Lemaigre
 * @license    MIT
 */

class GlueDB_Database_MySQL extends GlueDB_Database {
	/**
	 * @var string The hostname on which the database server resides.
	 */
	protected $host = 'localhost';

	/**
	 * @var string The port number where the database server is listening.
	 */
	protected $port;

	/**
	 * @var string The name of the database.
	 */
	protected $dbname;

	/*
	 * Builds DSN string ( http://www.php.net/manual/en/ref.pdo-mysql.connection.php ).
	 *
	 * @returns string
	 */
	protected function dsn() {
		// Builds DSN :
		$dsn = 'mysql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->dbname;

		// Unset connection parameters to make sure no forgotten debug message
		// displays them unintentionaly to a user :
		$this->host = null;
		$this->port = null;
		$this->dbname = null;

		return $dsn;
	}
	
	/**
	 * Creates a dialect object suitable for communicating with MySQL.
	 *
	 * @return string
	 */
	protected function create_dialect() {
		return new GlueDB_Dialect_MySQL;
	}
}