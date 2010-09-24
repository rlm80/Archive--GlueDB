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
 *   type cast the values coming from the database according to the underlying column type.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Table {
	/**
	 * @var array Virtual tables instances cache.
	 */
	static protected $instances = array();

	/**
	 * @var string Name of this virtual table, as it will be refered to in the query builder.
	 */
	protected $name;

	/**
	 * @var string	Name of the database that owns the real underlying table.
	 */
	protected $dbname;

	/**
	 * @var string	Real underlying table name.
	 */
	protected $dbtable;

	/**
	 * @var array Primary key columns of this table.
	 */
	protected $pk;

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
		// Init name :
		$this->name = $name;

		// Init properties :
		if ( ! isset($this->dbtable))	$this->dbtable	= $this->init_dbtable();
		if ( ! isset($this->dbname))	$this->dbname	= $this->init_dbname();

		// Create columns :
		$this->columns = $this->init_columns();

		// Create pk :
		$this->pk = $this->init_pk();
	}

	/**
	 * Returns the name of the real underlying table.
	 *
	 * @return array
	 */
	protected function init_dbtable() {
		return $this->name;
	}

	/**
	 * Returns the name of the database that owns the real underlying table.
	 *
	 * @return string
	 */
	protected function init_dbname() {
		return GlueDB_Database::DEFAULTDB; // TODO Do something better than this. We should look into each
										   // available database and search for one that owns the real table.
	}

	/**
	 * TODO
	 *
	 * @return string
	 */
	protected function init_pk() {
		return array();
	}

	/**
	 * Generates the columns by database introspection.
	 *
	 * This function makes use of get_column_alias() and get_column_formatter() to do
	 * the job. These functions are the ones that you may want to redefine, you
	 * shouldn't have to redefine this one.
	 *
	 * @return array
	 */
	private function init_columns() {
		$columns = array();
		$info_table = $this->db()->table_info($this->dbtable);
		foreach ($info_table['columns'] as $info_column) {
			$columns[$alias] = new GlueDB_Column(
					$this,
					$info_column['column'],
					$info_column['type'],
					$info_column['nullable'],
					$info_column['maxlength'],
					$info_column['precision'],
					$info_column['scale'],
					$info_column['default'],
					$info_column['auto']
				);
		}
		return $columns;
	}

	/**
	 * Returns the alias under which a real column will be known in PHP-land.
	 *
	 * This alias defines how you may refer to the column in the query builder. You
	 * may redefine this if, for example, you wish to change the name of a real column
	 * without impacting the PHP application, or the other way around.
	 *
	 * @param GlueDB_Column $column
	 *
	 * @return string
	 */
	public function get_column_alias(GlueDB_Column $column) {
		return $column->dbcolumn();
	}

	/**
	 * Returns the appropriate formatter for given column.
	 *
	 * You may want to redefine this if, for example, it's not possible for GlueDB to
	 * guess the right PHP type from the db type (sqlite ?) or because you want some
	 * funky formatting like serialization.
	 *
	 * @param GlueDB_Column $column
	 *
	 * @return GlueDB_Formatter
	 */
	public function get_column_formatter(GlueDB_Column $column) {
		return $this->db()->get_formatter($column);
	}

	/**
	 * Returns the database object this virtual table is stored into.
	 *
	 * @return GlueDB_Database
	 */
	public function db() {
		return gluedb::db($this->dbname);
	}

	/**
	 * Returns the primary key columns of this table.
	 *
	 * @return array
	 */
	public function pk() {
		return $this->pk;
	}

	/**
	 * Returns the database name this virtual table is stored into.
	 *
	 * @return string
	 */
	public function dbname() {
		return $this->dbname;
	}

	/**
	 * Returns the real underlying table name.
	 *
	 * @return string
	 */
	public function dbtable() {
		return $this->dbtable;
	}

	/**
	 * Returns a table helper for this table.
	 *
	 * @param GlueDB_Query	$query	Query in the context of which the table helper is required.
	 * @param string		$alias	Alias of current table in that query.
	 *
	 * @return GlueDB_Helper_Table
	 */
	public function helper() {
		return new GlueDB_Fragment_Helper_Table($this);
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
