<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base PostgreSQL database class.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Database_PostgreSQL extends GlueDB_Database {
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
	 * Builds DSN string ( http://www.php.net/manual/en/ref.pdo-pgsql.connection.php ).
	 *
	 * @returns string
	 */
	protected function dsn() {
		// Builds DSN :
		$dsn = 'pgsql:host=' . $this->host . ';port=' . $this->port . ';dbname=' . $this->dbname;

		// Unset connection parameters to make sure no forgotten debug message
		// displays them unintentionaly to a user :
		$this->host = null;
		$this->port = null;
		$this->dbname = null;

		return $dsn;
	}

	/**
	 * Creates a dialect object suitable for communicating with PostgreSQL.
	 *
	 * @return string
	 */
	protected function create_dialect() {
		return new GlueDB_Dialect_PostgreSQL;
	}

	/**
	 * Returns structured information about the columns and primary key of a real database table.
	 * Columns are returned alphabetically ordered.
	 *
	 * Structure :
	 * array(
	 * 		'columns'	=> array(
	 * 			'name'		=> < Column name >
	 *			'dbtype'	=> < Native database type >
	 *			'phptype'	=> < Appropriate PHP type to represent column values >
	 *			'nullable'	=> < Whether or not the column is nullable >
	 *			'maxlength'	=> < Maximum length of a text column >
	 *			'precision' => < Precision of the column (number of significant digits) >
	 *			'scale' 	=> < Scale of the column (number of significant digits to the right of the decimal point) >
	 *			'default'	=> < Default value of the column (stored as is from the database, not type casted) >
	 *			'auto'		=> <Whether or not the column auto-incrementing >
	 * 		)
	 * 		'pk'		=> array(
	 * 			0 => < columns 1>
	 * 			1 => < columns 1>
	 * 			...
	 * 		)
	 * )
	 *
	 * Be aware that this function is totally ignorant of any virtual table you may have
	 * defined explicitely ! It's mostly useful internally to query the real underlying
	 * database schema. Users should use the introspection API instead.
	 *
	 * @return array
	 */
	public function real_table($name) {
		throw new Kohana_Exception("The GlueDB_Database::real_table function isn't implemeted for postgre. If you want this feature, please fork the project on github and add it. The docs to do it are here : http://www.postgresql.org/docs/8.1/interactive/information-schema.html");
	}

	/**
	 * Returns all tables present in current database as an array of table names.
	 *
	 * Be aware that this function is totally ignorant of any virtual table
	 * you may have defined explicitely !
	 *
	 * @return array Array of table names, numerically indexed, alphabetically ordered.
	 */
	public function real_tables() {
		throw new Kohana_Exception("The GlueDB_Database::real_tables function isn't implemeted for postgre. If you want this feature, please fork the project on github and add it. The docs to do it are here : http://www.postgresql.org/docs/8.1/interactive/information-schema.html");
	}
}