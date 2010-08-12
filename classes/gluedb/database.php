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
	 * Constructor.
	 */
	public function __construct() {
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

	/*
	 * Same as PDO::query if statement is a string. If statement is a data structure
	 * representing an SQL query, it is first compiled into an SQL string before being
	 * handed over to the PDO corresponding method.
	 *
	 * @see PDO::query()
	 */
	public function query($statement) {
		$sql = $this->compile($statement);
		return parent::query($sql);
	}

	/*
	 * Same as PDO::prepare if statement is a string. If statement is a data structure
	 * representing an SQL query, it is first compiled into an SQL string before being
	 * handed over to the PDO corresponding method.
	 *
	 * @see PDO::prepare()
	 */
	public function prepare($statement, $driver_options = null) {
		$sql = $this->compile($statement);
		return parent::prepare($sql);
	}

	/*
	 * Same as PDO::exec if statement is a string. If statement is a data structure
	 * representing an SQL query, it is first compiled into an SQL string before being
	 * handed over to the PDO corresponding method.
	 *
	 * @see PDO::exec()
	 */
	public function exec($statement) {
		$sql = $this->compile($statement);
		return parent::exec($sql);
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
		$class = 'GlueDB_Database_'.ucfirst($name);
		return new $class;
	}
}