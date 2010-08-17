<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base MySQL database class.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
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
		$dsn = 'mysql:host=' . $this->host . ';';
		if (isset($this->port))
			$dsn .= 'port=' . $this->port . ';';
		if (isset($this->dbname))
			$dsn .= 'dbname=' . $this->dbname . ';';

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

	/**
	 * Returns structured information about a real database table and its columns.
	 * Columns are returned alphabetically ordered.
	 *
	 * Be aware that this function is totally ignorant of any virtual table you may have
	 * defined explicitely ! It's mostly useful internally to query the real underlying
	 * database schema. Users should use the introspection API instead.
	 *
	 * @return array
	 */
	public function table_info($name) {
		// Get columns information :
		$stmt = $this->prepare("
			SELECT
				column_name,
				data_type,
				is_nullable,
				column_default,
				character_maximum_length,
				numeric_precision,
				numeric_scale,
				extra
			FROM
				information_schema.columns
			WHERE
				table_schema = :dbname AND
				table_name = :tablename
		")->execute(array(
			':dbname'		=> $this->dbname,
			':tablename'	=> $name
		));

		// Create columns data structure :
		$columns = array();
		while ($row = $stmt->fetch()) {
			$name		= trim(strtolower($row[0]));
			$dbtype		= trim(strtolower($row[1]));
			$phptype	= $this->dialect->phptype($dbtype);
			$nullable	= (boolean) $row[2];
			$default	= $row[3];
			$max		= isset($row[4]) ? (float)   $row[4] : null;
			$precision	= isset($row[5]) ? (integer) $row[5] : null;
			$scale		= isset($row[6]) ? (integer) $row[6] : null;
			$auto		= trim(strtolower($row[7])) === 'auto_increment' ? true : false;
			$columns[] = array(
				'name'		=> $name,		// Column name.
				'dbtype'	=> $dbtype,		// Native database type.
				'phptype'	=> $phptype,	// Appropriate PHP type to represent column values.
				'nullable'	=> $nullable,	// Whether or not the column is nullable.
				'max'		=> $max,		// Max length of a text column, or maximum value of a numeric column.
				'precision' => $precision,	// Precision of the column.
				'scale' 	=> $scale,		// Scale of the column.
				'default'	=> $default,	// Default value of the column (stored as is from the database, not type casted).
				'auto'		=> $auto,		// Whether or not the column auto-incrementing.
			);
		}
		sort($columns);

		// Create indexes data structure :
		// TODO

		return array(
				'columns' => $columns
			);
	}

	/**
	 * Returns all tables present in current database as an array of table names.
	 *
	 * Be aware that this function is totally ignorant of any virtual table
	 * you may have defined explicitely !
	 *
	 * @return array Array of table names, numerically indexed, alphabetically ordered.
	 */
	public function tables() {
		$stmt = $this->prepare("SELECT table_name FROM information_schema.tables WHERE table_schema = :dbname");
		$stmt->execute(array(':dbname' => $this->dbname));
		$tables = array();
		while ($table = $stmt->fetchColumn())
			$tables[] = $table;
		sort($tables);
		return $tables;
	}
}