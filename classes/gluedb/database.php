<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base database class.
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
	 * @var boolean Locks constructor access from anywhere but self::create.
	 * 				This ensures correct singleton behaviour even though constructor must
	 * 				remain public because parent constructor is.
	 */
	private static $constuctor_locked = TRUE;

	/**
	 * Constructor.
	 *
	 * @param string $name
	 */
	public function __construct($name) {
		// Check lock :
		if (self::$constuctor_locked)
			throw Kohana_Exception('Cannot instanciate database directly. Call GlueDB::db($name) instead.');

		// Set identifier :
		$this->name = $name;

		// Set PDO options :
		$options[PDO::ATTR_ERRMODE]		= PDO::ERRMODE_EXCEPTION;
		$options[PDO::ATTR_PERSISTENT]	= $this->persistent;

		// Call parent constructor :
		parent::__construct($this->dsn(), $this->username, $this->password, $this->options);

		// Set connection charset :
		$this->exec('SET NAMES ' . $this->quote($this->charset));

		// Unset connection parameters for security, to make sure no forgotten debug message
		// displays them unintentionaly to a user :
		$this->username = null;
		$this->password = null;
	}

	/**
	 * Returns the DSN to the current database.
	 *
	 * @returns string
	 */
	abstract protected function dsn();

	/**
	 * Returns a select query data structure meant to query this database.
	 *
	 * @return GlueDB_Query_Select
	 */
	public function select() {
		return new GlueDB_Query_Select($this);
	}

	/**
	 * Returns the table object of given name for current database.
	 * Returned table may be real or virtual.
	 *
	 * @return GlueDB_Table
	 */
	public function table($name) {
		return GlueDB_Table::get($this->name, $name);
	}

	/**
	 * Quotes an identifier according to the underlying rdbms conventions.
	 *
	 * @param string $identifier
	 *
	 * @return
	 */
	public function quote_identifier($identifier) {
		return '"' . $identifier . '"';
	}

	/**
	 * Compiles a datastructure representing an SQL query into an SQL string.
	 *
	 * @param mixed $statement
	 *
	 * @return string
	 */
	public function compile($statement) {
		if (is_string($statement))
			return $statement;
		else {
			// This is where the magic happens.
		}
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