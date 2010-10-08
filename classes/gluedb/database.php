<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base database class.
 *
 * A database object is a PDO instance connected to a specific database. This
 * class extends PDO and adds to it a unified interface for database introspection
 * and a query compiler to generate RDBMS specific SQL queries.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Database extends PDO {
	/**
	 * @var string Name of the default database.
	 */
	const DEFAULTDB = 'Primary';

	/**
	 * @var array Database instances cache.
	 */
	static protected $instances = array();

	/**
	 * @var string Identifier of the current database.
	 */
	protected $name;

	/**
	 * @var string The user name for the DSN string.
	 */
	protected $username;

	/**
	 * @var string The password for the DSN string.
	 */
	protected $password;

	/**
	 * @var string A key=>value array of driver-specific connection options.
	 */
	protected $options = array();

	/**
	 * @var string Connection charset.
	 */
	protected $charset = 'utf8';

	/**
	 * @var boolean Locks constructor access from anywhere but self::create.
	 * 				This ensures correct singleton behaviour even though constructor must
	 * 				remain public because parent constructor is. The other solution was
	 * 				to wrap the PDO instance into this class instead of extending PDO,
	 * 				but this is not good because I wish to expose all PDO features.
	 */
	private static $constuctor_locked = TRUE;

	/**
	 * Constructor.
	 *
	 * @param string $name Identifier of this database.
	 */
	public function __construct($name) {
		// Check lock :
		if (self::$constuctor_locked)
			throw Kohana_Exception('Cannot instanciate databases directly. Call GlueDB::db($name) instead.');

		// Set identifier :
		$this->name = $name;

		// Set PDO options :
		$this->options[PDO::ATTR_ERRMODE]			= PDO::ERRMODE_EXCEPTION;
		$this->options[PDO::ATTR_STATEMENT_CLASS]	= array('GlueDB_Statement', array($this));

		// Call parent constructor to establish connection :
		parent::__construct($this->dsn(), $this->username, $this->password, $this->options);

		// Unset connection parameters for security, to make sure no forgotten debug message
		// displays them unintentionaly to a user :
		$this->username = null;
		$this->password = null;

		// Set connection charset :
		$this->set_charset();
	}

	/**
	 * Returns the DSN pointing to the current database.
	 *
	 * @returns string
	 */
	abstract protected function dsn();

	/**
	 * Getter for database name.
	 *
	 * @return string
	 */
	public function name() {
		return $this->name;
	}

	/**
	 * Issues the right query to set current connection charset. This is probably
	 * RDBMS specific so it's factored out of the constructor into a function
	 * that can be redefined if necessary.
	 */
	protected function set_charset() {
		$this->exec('SET NAMES ' . $this->quote($this->charset));
	}

	/**
	 * Quotes an identifier according to current database conventions.
	 *
	 * @param string $identifier
	 *
	 * @return string
	 */
	public function compile_identifier($identifier) {
		return '"' . $identifier . '"';
	}


	/**
	 * Quotes a value for inclusion into an SQL query.
	 *
	 * Extends PDO::quote to deal with any PHP types (especially arrays), not just strings. Don't
	 * redefine this, instead redefine one of its factor methods (compile_array, compile_integer, etc.).
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	public function compile_value($value) {
		if (is_string($value))
			return $this->compile_string($value);
		elseif (is_array($value))
			return $this->compile_array($value);
		elseif (is_bool($value))
			return $this->compile_bool($value);
		elseif (is_integer($value))
			return $this->compile_integer($value);
		elseif (is_float($value))
			return $this->compile_float($value);
		elseif (is_null($value))
			return $this->compile_null($value);
		else
			throw new Kohana_Exception("Cannot quote objects.");
	}

	/**
	 * Quotes a string for inclusion into an SQL query.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	protected function compile_string($value) {
		return parent::quote($value);
	}

	/**
	 * Quotes an array for inclusion into an SQL query.
	 *
	 * @param array $value
	 *
	 * @return string
	 */
	protected function compile_array(array $value) {
		// Empty arrays are not valid :
		if (count($value) === 0)
			throw new Kohana_Exception("Cannot quote empty array.");

		// Recursion :
		foreach ($value as $val)
			$arr[] = $this->compile_value($val);

		return '(' . implode(',', $arr) . ')';
	}

	/**
	 * Quotes an integer for inclusion into an SQL query.
	 *
	 * @param integer $value
	 *
	 * @return string
	 */
	protected function compile_integer($value) {
		return (string) $value;
	}

	/**
	 * Quotes an boolean for inclusion into an SQL query.
	 *
	 * @param integer $value
	 *
	 * @return string
	 */
	protected function compile_bool($value) {
		return $value ? 'TRUE' : 'FALSE';
	}

	/**
	 * Quotes a float for inclusion into an SQL query.
	 *
	 * @param float $value
	 *
	 * @return string
	 */
	protected function compile_float($value) {
		return (string) $value;
	}

	/**
	 * Returns SQL representation of null.
	 *
	 * @param null $value
	 *
	 * @return string
	 */
	protected function compile_null($value) {
		return 'NULL';
	}

	/**
	 * Returns SQL string for something that needs an alias.
	 *
	 * @param string $sql SQL of the thing that needs an alias.
	 * @param string $alias Alias.
	 *
	 * @return string
	 */
	protected function compile_alias($sql, $alias) {
		return $sql . ' AS ' . $this->compile_identifier($alias);
	}

	/**
	 * Returns the appropriate formatter for given column.
	 *
	 * @param GlueDB_Column $column
	 *
	 * @return GlueDB_Formatter
	 */
	abstract public function get_formatter(GlueDB_Column $column);

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
	public abstract function table_info($name);

	/**
	 * Returns all tables present in current database as an array of table names.
	 *
	 * Be aware that this function is totally ignorant of any virtual table
	 * you may have defined explicitely !
	 *
	 * @return array Array of table names, numerically indexed, alphabetically ordered.
	 */
	abstract public function real_tables();

	/**
	 * Lazy loads a database object, stores it in cache, and returns it.
	 *
	 * @param string $name
	 *
	 * @return GlueDB_Database
	 */
	static public function get($name) {
		$name = strtolower($name);
		if( ! isset(self::$instances[$name]))
			self::$instances[$name] = self::create($name);
		return self::$instances[$name];
	}

	/**
	 * Returns a new database instance. Throws class not found exception if
	 * no class is defined for given database.
	 *
	 * @param string $name
	 *
	 * @return GlueDB_Database
	 */
	static protected function create($name) {
		// Class name :
		$class = 'GlueDB_Database_'.ucfirst($name);

		// Unlock constructor, create instance and relock constructor :
		self::$constuctor_locked = false;
		$instance = new $class($name);
		self::$constuctor_locked = true;

		return $instance;
	}
}