<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base table class.
 * 
 * Information about a database table is represented as a table object that is
 * instanciated on first access and then cached. There are two types of table objects :
 * - simple tables : they map directly to a database table,
 * - composite tables : they map to more than one database tables, joined together by primary key.
 * 
 * Representing tables as objects this way makes it possible to define a very convenient
 * database introspection API. It also sets up a framework that makes it easy to define
 * computed columns, columns formaters, composite tables, etc...in a way that is totally
 * transparent to the application layers that sits on top of this library.
 * 
 * Moving this complexity down at the DBAL level makes the rest of the application easier
 * to code and easier to understand.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Table {
	/**
	 * @var array Tables instances cache.
	 */
	static protected $instances = array();

	/**
	 * @var string	The database this table (or its components if this is a
	 * 				composite table) is stored into.
	 */
	protected $database;

	/**
	 * @var string Name of this table, as it will be refered to in queries.
	 */
	protected $name;
	
	/**
	 * @var array Columns of this table.
	 */
	protected $columns;	
	
	/**
	 * @var array Primary key columns of this table.
	 */
	protected $pk;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Database	$database	Database.
	 * @param string			$name		Table name.
	 */
	protected function __construct($database, $name) {
		// Set database :
		$this->database = $database;

		// Set table name :
		$this->name = $name;
	}

	/**
	 * Returns a table helper for this table.
	 *
	 * @param GlueDB_Query	$query	Query in the context of which the table helper is required.
	 * @param string		$alias	Alias of current table in that query.
	 *
	 * @return GlueDB_Helper_Table
	 */
	public function helper(GlueDB_Query $query, $alias) {
		return new GlueDB_Helper_Table($query, $this, $alias);
	}

	/**
	 * Returns an update query object for current table.
	 *
	 * @return GlueDB_Query_Update
	 */
	public function update() {
		return new GlueDB_Query_Update($this);
	}

	/**
	 * Returns a delete query object for current table.
	 *
	 * @return GlueDB_Query_Delete
	 */
	public function delete() {
		return new GlueDB_Query_Delete($this);
	}

	/**
	 * Returns a insert query object for current table.
	 *
	 * @return GlueDB_Query_Insert
	 */
	public function insert() {
		return new GlueDB_Query_Insert($this);
	}

	/**
	 * Lazy loads a table object, stores it in cache, and returns it.
	 *
	 * @param string $dbname	Database name.
	 * @param string $name		Table name.
	 *
	 * @return GlueDB_Table
	 */
	static public function get($dbname, $name) {
		$dbname	= strtolower($dbname);
		$name	= strtolower($name);
		if( ! isset(self::$instances[$dbname][$name]))
			self::$instances[$dbname][$name] = self::create($dbname, $name);
		return self::$instances[$dbname][$name];
	}

	/**
	 * Returns a new table instance. Throws class not found exception if
	 * no class is defined for given table.
	 *
	 * @param string $name
	 *
	 * @return object
	 */
	static protected function create($dbname, $name) {
		$db		= GlueDB::db($dbname);
		$class	= 'GlueDB_Table_' . ucfirst($dbname) . '_' . ucfirst($name);
		if (class_exists($class))
			return new $class($db, $name);
		else 
			return new GlueDB_Table_Simple($db, $name);
	}
}
