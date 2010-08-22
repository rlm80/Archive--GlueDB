<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base virtual table class.
 *
 * The tables you are referring to when you work with the query builder or with the
 * introspection API are not real database tables. They are PHP objects called virtual
 * tables. Just like real tables, virtual tables have names and columns. By default,
 * all virtual tables map to the corresponding table in the underlying database and
 * have the same columns so you actually don't notice that this system even exists at all.
 *
 * But you may define your own virtual tables. You do so by creating a class called
 * GlueDB_Table_<virtual table name> that extends GlueDB_Table.
 * 
 * You may want to do that if you want to :
 * - have a virtual table point to a real table that has a different name,
 * - have a virtual table column point to a real column that has a different name,
 * - set up a GlueDB_Formatter for a column, other than the default one that simply
 *   type cast the values according to the underlying database column type.
 *   
 * There are plans to support virual tables that map to a join of real tables in future
 * versions. Those will be called composite virtual tables.
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
	 * @var string Name of this virtual table, as it will be refered to in the query builder.
	 */
	protected $name;	

	/**
	 * @var array Columns of this table.
	 */
	protected $columns;

	/**
	 * Constructor.
	 *
	 * @param string $name Table name.
	 */
	protected function __construct($name) {
		// Init table name :
		$this->name = $name;
	}

	/**
	 * Returns the database object this virtual table is stored into.
	 *
	 * @return GlueDB_Database
	 */
	abstract public function db();
	
	/**
	 * Returns the primary key columns of this table.
	 *
	 * @return array
	 */
	abstract public function pk();
	
	/**
	 * Underlying database table names.
	 *
	 * @return array
	 */
	abstract public function dbtables();	

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
	 * Returns a column.
	 *
	 * @param string $name
	 *
	 * @return GlueDB_Column_Base
	 */
	public function column($name) {
		return $this->columns[$name];
	}

	/**
	 * Loads a virtual table, stores it in cache, and returns it.
	 *
	 * @param string $name Virtual table name.
	 *
	 * @return GlueDB_Table
	 */
	static public function get($name) {
		$name = strtolower($name);
		if( ! isset(self::$instances[$name]))
			self::$instances[$name] = self::create($name);
		return self::$instances[$name];
	}

	/**
	 * Returns a new virtual table instance.
	 *
	 * @param string $name
	 *
	 * @return GlueDB_Table
	 */
	static protected function create($name) {
		$class = 'GlueDB_Table_' . ucfirst($name);
		if (class_exists($class))
			return new $class($name);
		else
			return new GlueDB_Table($name);
	}
}
