<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base virtual table class.
 *
 * The tables you are referring to when you work with the query builder or with the
 * introspection API are not real database tables. They are PHP objects called virtual
 * tables. Just like real tables, virtual tables have names, columns and belong to a
 * database. By default, all virtual tables map to the corresponding table in the underlying
 * database and have the same columns so you actually don't notice that this system even
 * exists at all.
 *
 * But you may define your own virtual tables.
 *
 * You do so by creating a class called GlueDB_Table_<database name>_<virtual table name> that
 * extends either :
 * - GlueDB_Table : such a virtual table maps directly to a real database table,
 * - GlueDB_Table_Composite : such a virtual table maps to a group of several other virtual tables
 * 							  joined together by primary key.
 *
 * Representing tables as objects this way makes it possible to define a very convenient
 * database introspection API. It also sets up a framework that makes it easy for the user
 * to define computed columns and columns formaters. It adds a level of indirection between
 * the application and the database. Composite virtual tables allows to emulate in PHP
 * updatable, deletable and insertable views, something that RDBMS don't support or
 * support only partially.
 *
 * Moving this complexity down at the DBAL level makes the rest of the application easier
 * to code and easier to understand.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Table_Base {
	/**
	 * @var array Tables instances cache.
	 */
	static protected $instances = array();

	/**
	 * @var string	Name of the database this virtual table belongs to. A virtual table may not map
	 * 				to real tables that belong to another database as this one.
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
	 * @param string	$database	Database name.
	 * @param string	$name		Table name.
	 */
	protected function __construct($database, $name) {
		// Set database :
		$this->database = $database;

		// Set table name :
		$this->name = $name;

		// Create columns :
		$this->columns = $this->create_columns();
	}


	/**
	 * Returns the columns of this virtual table.
	 *
	 * @return array
	 */
	abstract protected function create_columns();

	/**
	 * Returns the database object this virtual table is stored into.
	 *
	 * @return GlueDB_Database
	 */
	public function db() {
		return gluedb::db($this->database);
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
	 * Returns the columns of this table.
	 *
	 * @return array
	 */
	public function columns() {
		return $this->columns;
	}

	/**
	 * Returns the column object of given name for this table.
	 *
	 * @param string $table Table name.
	 *
	 * @return GlueDB_Column_Base
	 */
	public function __get($column) {
		if (isset($this->columns[$column]))
			return $this->columns[$column];
		else
			; // TODO decide what to return here...null ? Dummy column object meaning undefined ?
	}

	/**
	 * Lazy loads a table object, stores it in cache, and returns it.
	 *
	 * TODO : cache serialized virtual tables on disk
	 *
	 * @param string $database	Database name.
	 * @param string $name		Table name.
	 *
	 * @return GlueDB_Table_Base
	 */
	static public function get($database, $name) {
		$database	= strtolower($database);
		$name		= strtolower($name);
		if( ! isset(self::$instances[$database][$name]))
			self::$instances[$database][$name] = self::create($database, $name);
		return self::$instances[$database][$name];
	}

	/**
	 * Returns a new table instance.
	 *
	 * @param string $name
	 *
	 * @return GlueDB_Table_Base
	 */
	static protected function create($database, $name) {
		$class = 'GlueDB_Table_' . ucfirst($database) . '_' . ucfirst($name);
		if (class_exists($class))
			return new $class($database, $name);
		else
			return new GlueDB_Table($database, $name);
	}
}
