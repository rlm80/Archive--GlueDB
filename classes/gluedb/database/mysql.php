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
	 * Returns structured information about the columns and primary key of a real database table.
	 * Columns are returned alphabetically ordered.
	 *
	 * Structure :
	 * array(
	 * 		'columns' => array(
	 * 			0 => array (
	 * 				'dbcolumn'		=> < Column name >
	 *				'dbtype'		=> < Native database type >
	 *				'dbnullable'	=> < Whether or not the column is nullable >
	 *				'dbmaxlength'	=> < Maximum length of a text column >
	 *				'dbprecision' 	=> < Precision of the column >
	 *				'dbscale' 		=> < Scale of the column >
	 *				'dbdefault'		=> < Default value of the column (stored as is from the database, not type casted) >
	 *				'dbauto'		=> <Whether or not the column auto-incrementing >
	 *			)
	 *			1 => ...
	 *			...
	 * 		)
	 * 		'pk' => array(
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
		// Query information schema to get columns information :
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
		");
		$stmt->execute(array(
			':dbname'		=> $this->dbname,
			':tablename'	=> $name
		));

		// Create columns data structure :
		$columns = array();
		while ($row = $stmt->fetch()) {
			$columns[] = array(
				'dbcolumn'		=> trim(strtolower($row[0])),
				'dbtype'		=> trim(strtolower($row[1])),
				'dbnullable'	=> (boolean) $row[2],
				'dbdefault'		=> $row[3],
				'dbmaxlength'	=> isset($row[4]) ? (integer) $row[4] : null,
				'dbprecision' 	=> isset($row[5]) ? (integer) $row[5] : null,
				'dbscale' 		=> isset($row[6]) ? (integer) $row[6] : null,
				'dbauto'		=> trim(strtolower($row[7])) === 'auto_increment' ? true : false,
			);
		}
		sort($columns);

		// Query information schema to get pk information :
		$stmt = $this->prepare("
			SELECT
				column_name
			FROM
				information_schema.statistics
			WHERE
				table_schema = :dbname AND
				table_name = :tablename AND
				index_name = 'PRIMARY'
			ORDER BY
				seq_in_index
		");
		$stmt->execute(array(
			':dbname'		=> $this->dbname,
			':tablename'	=> $name
		));

		// Create columns data structure :
		$pk = array();
		while ($row = $stmt->fetch())
			$pk[] = $row[0];

		return array(
				'columns'	=> $columns,
				'pk'		=> $pk,
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
	public function real_tables() {
		$stmt = $this->prepare("SELECT table_name FROM information_schema.tables WHERE table_schema = :dbname");
		$stmt->execute(array(':dbname' => $this->dbname));
		$tables = array();
		while ($table = $stmt->fetchColumn())
			$tables[] = $table;
		sort($tables);
		return $tables;
	}
}