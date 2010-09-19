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
	 * Returns structured information about the columns and primary key of a real database table.
	 * Columns are returned alphabetically ordered.
	 *
	 * Structure :
	 * array(
	 * 		'columns' => array(
	 * 			0 => array (
	 * 				'column'	=> < Column name >
	 *				'type'		=> < Native database type >
	 *				'nullable'	=> < Whether or not the column is nullable >
	 *				'maxlength'	=> < Maximum length of a text column >
	 *				'precision' => < Precision of the column >
	 *				'scale' 	=> < Scale of the column >
	 *				'default'	=> < Default value of the column (stored as is from the database, not type casted) >
	 *				'auto'		=> <Whether or not the column auto-incrementing >
	 *			)
	 *			1 => ...
	 *			...
	 * 		)
	 * 		'pk' => array(
	 * 			0 => < columns 0>
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
	public function table_info($name) {
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
				'column'	=> trim(strtolower($row[0])),
				'type'		=> trim(strtolower($row[1])),
				'nullable'	=> (boolean) $row[2],
				'default'	=> $row[3],
				'maxlength'	=> isset($row[4]) ? (integer) $row[4] : null,
				'precision' => isset($row[5]) ? (integer) $row[5] : null,
				'scale' 	=> isset($row[6]) ? (integer) $row[6] : null,
				'auto'		=> trim(strtolower($row[7])) === 'auto_increment' ? true : false,
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

	/**
	 * Returns the appropriate PHP type to represent given native database type.
	 *
	 * @param string $dbtype
	 *
	 * @return string TODO return formatter instead ?
	 */
	public function get_phptype($dbtype)  {
		// Extract first word from type (MySQL may return things like "float unsigned" sometimes) :
		if (preg_match('/^\S+/', $dbtype, $matches))
			$dbtype = $matches[0];

		// Convert type to upper case :
		$dbtype = strtoupper($dbtype);

		// Create appropriate formatter :
		switch ($dbtype) {
			// Integer types :
			case 'TINYINT'; case 'SMALLINT'; case 'MEDIUMINT'; case 'INT'; case 'BIGINT';
				$phptype = 'integer';
				break;

			// Real types :
			case 'FLOAT'; case 'DOUBLE'; case 'DECIMAL';
				$phptype = 'float';
				break;

			// Boolean types :
			case 'BIT';
				$phptype = 'boolean';
				break;

			// String types :
			case 'CHAR'; case 'VARCHAR'; case 'TINYTEXT'; case 'TEXT';
			case 'MEDIUMTEXT'; case 'LONGTEXT'; case 'ENUM'; case 'SET';
				$phptype = 'string';
				break;

			// Binary types :
			case 'BINARY'; case 'VARBINARY'; case 'TINYBLOB'; case 'BLOB';
			case 'MEDIUMBLOB'; case 'LONGBLOB';
				$phptype = 'string'; // TODO Is this the right thing to do ?
				break;

			// Date and time types :
			case 'DATE'; case 'DATETIME'; case 'TIME'; case 'TIMESTAMP'; case 'YEAR';
				$phptype = 'string'; // TODO Is this the right thing to do ?
				break;

			// Default :
			default;
				throw new Kohana_Exception("Unknown MySQL data type : " . $dbtype);
		}

		return $phptype;
	}

	/**
	 * Quotes an identifier according to MySQL conventions. Mysql uses back-ticks for this
	 * instead of the ANSI double quote standard character.
	 *
	 * @param string $identifier
	 *
	 * @return
	 */
	public function quote_identifier($identifier) {
		return '`' . $identifier . '`';
	}
}