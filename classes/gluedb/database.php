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
	 * @var boolean Whether or not the connection is persistent.
	 */
	protected $persistent = FALSE;

	/**
	 * @var GlueDB_Dialect The dialect suitable for communication with current database.
	 */
	protected $dialect;

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

		// Set SQL dialect :
		$this->dialect = $this->create_dialect();

		// Set PDO options :
		$this->options[PDO::ATTR_ERRMODE]		= PDO::ERRMODE_EXCEPTION;
		$this->options[PDO::ATTR_PERSISTENT]	= $this->persistent;

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
	 * Creates a dialect object suitable for communicating with current database.
	 *
	 * @return string
	 */
	protected function create_dialect() {
		return new GlueDB_Dialect_ANSI;
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
	 * Quotes an identifier according to current SQL dialect conventions.
	 *
	 * Forwards call to dialect object.
	 *
	 * @param string $identifier
	 *
	 * @return
	 */
	public function quote_identifier($identifier) {
		return $this->dialect->quote_identifier($identifier);
	}

	/**
	 * Compiles a datastructure representing an SQL query into an SQL string
	 * according to current SQL dialect conventions.
	 *
	 * Forwards call to dialect object.
	 *
	 * @param mixed $statement
	 *
	 * @return string
	 */
	public function compile($statement) {
		return $this->dialect->compile($statement);
	}

	/**
	 * Returns a select query data structure meant to query this database.
	 *
	 * @return GlueDB_Query_Select
	 */
	public function select() {
		return new GlueDB_Query_Select($this);
	}

	/**
	 * Returns structured information about a real database table and its columns.
	 * Columns are returned alphabetically ordered.
	 *
	 * Be aware that this function is totally ignorant of any virtual table
	 * you may have defined explicitely !
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
	abstract public function tables();

	/**
	 * Returns the virtual table of given name for current database.
	 * This should always be used internally instead of __get, because
	 * table names may clash with protected properties of this class.
	 *
	 * @param string $table Table name.
	 *
	 * @return GlueDB_Table
	 */
	public function table($table) {
		return GlueDB_Table::get($this->name, $table);
	}

	/**
	 * Returns the table object of given name for current database.
	 * Returned table may be simple or composite.
	 *
	 * @param string $table Table name.
	 *
	 * @return GlueDB_Table
	 */
	public function __get($table) {
		return $this->table($table);
	}

	/**
	 * Lazy loads a database object, stores it in cache, and returns it.
	 *
	 * @param string $name
	 *
	 * @return object
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
	 * @return object
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