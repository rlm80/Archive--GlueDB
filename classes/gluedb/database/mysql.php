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
	
	// Native database types :
	const TYPE_TINYINT		= 'TINYINT';
	const TYPE_SMALLINT		= 'SMALLINT';
	const TYPE_MEDIUMINT	= 'MEDIUMINT';
	const TYPE_INT			= 'INT';
	const TYPE_BIGINT		= 'BIGINT';
	const TYPE_FLOAT		= 'FLOAT';
	const TYPE_DOUBLE		= 'DOUBLE';
	const TYPE_DECIMAL		= 'DECIMAL';
	const TYPE_BIT			= 'BIT';
	const TYPE_CHAR			= 'CHAR';
	const TYPE_VARCHAR		= 'VARCHAR';
	const TYPE_TINYTEXT		= 'TINYTEXT';
	const TYPE_TEXT			= 'TEXT';
	const TYPE_MEDIUMTEXT	= 'MEDIUMTEXT';
	const TYPE_LONGTEXT		= 'LONGTEXT';
	const TYPE_BINARY		= 'BINARY';
	const TYPE_VARBINARY	= 'VARBINARY';
	const TYPE_TINYBLOB		= 'TINYBLOB';
	const TYPE_BLOB			= 'BLOB';
	const TYPE_MEDIUMBLOB	= 'MEDIUMBLOB';
	const TYPE_LONGBLOB		= 'LONGBLOB';
	const TYPE_ENUM			= 'ENUM';
	const TYPE_SET			= 'SET';
	const TYPE_DATE			= 'DATE';
	const TYPE_DATETIME		= 'DATETIME';
	const TYPE_TIME			= 'TIME';
	const TYPE_TIMESTAMP	= 'TIMESTAMP';
	const TYPE_YEAR			= 'YEAR';	

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
	 * Returns the default formatter object for the given native MySQL type.
	 * 
	 * @return GlueDB_Formatter
	 */
	public function get_default_formatter($type) {
		// Extract first word from type (MySQL may return things like "float unsigned" sometimes) :
		if (preg_match('/^\S+/', $type, $matches))
			$type = $matches[0];
			
		// Convert type to upper case :
		$type = strtoupper($type);
		
		// Create appropriate formatter :
		switch ($type) {
			// Integer types :
			case self::TYPE_TINYINT;
			case self::TYPE_SMALLINT;
			case self::TYPE_MEDIUMINT;
			case self::TYPE_INT;
			case self::TYPE_BIGINT;
				$formatter = new GlueDB_Formatter_Integer;
				break;
			
			// Real types :
			case self::TYPE_FLOAT;
			case self::TYPE_DOUBLE;
			case self::TYPE_DECIMAL;
				$formatter = new GlueDB_Formatter_Float;
				break;
			
			// Boolean types :
			case self::TYPE_BIT;
				$formatter = new GlueDB_Formatter_Boolean;
				break;
	
			// Character types :
			case self::TYPE_CHAR;
			case self::TYPE_VARCHAR;
			case self::TYPE_TINYTEXT;
			case self::TYPE_TEXT;
			case self::TYPE_MEDIUMTEXT;
			case self::TYPE_LONGTEXT;
			case self::TYPE_ENUM;
			case self::TYPE_SET;
				$formatter = new GlueDB_Formatter_String;
				break;
			
			// Binary types :
			case self::TYPE_BINARY;
			case self::TYPE_VARBINARY;
			case self::TYPE_TINYBLOB;
			case self::TYPE_BLOB;
			case self::TYPE_MEDIUMBLOB;
			case self::TYPE_LONGBLOB;
				$formatter = new GlueDB_Formatter_String; // TODO Is this the right thing to do ?
				break;
			
			// Time types :
			case self::TYPE_DATE;
			case self::TYPE_DATETIME;
			case self::TYPE_TIME;
			case self::TYPE_TIMESTAMP;
			case self::TYPE_YEAR;
				$formatter = new GlueDB_Formatter_String; // TODO Is this the right thing to do ?
				break;
				
			// Default :
			default;
				throw new Kohana_Exception("Unknown MySQL native type " . $type);
		}
	} 
	
	/**
	 * Returns all tables present in current database as an array of table names. Be
	 * aware that this function is totally ignorant of any GlueDB_Table class you may
	 * have defined explicitely !
	 * 
	 * TODO : cache result ?
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