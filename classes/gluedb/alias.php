<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Table - alias pair.
 * 
 * You can think of this as a specific appearance of a table in a query that may
 * include the same table more than once. Tables are distinguished with aliases and
 * each table - alias pair is stored in an object of this class. The object can be
 * later used to refer to the columns of this specific instance of the table in the query.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Alias {
	/**
	 * @var array Alias pool.
	 */
	static protected $aliases = array();
	
	/**
	 * @var GlueDB_Table Table.
	 */
	protected $table;
	
	/**
	 * @var string Alias of the table in the context of some query.
	 */
	protected $alias;
	
	/**
	 * @var array Column SQL cache.
	 */
	protected $columns_sql = array();
	
	/**
	 * @var array Table SQL cache.
	 */
	protected $table_sql;	
	
	/**
	 * Constructor.
	 * 
	 * @param string $table_name
	 * @param string $alias
	 */
	public function __construct($table_name, $alias = null) {
		$this->table = gluedb::table($table_name);
		$this->alias = $alias;
	}
	
	/**
	 * Returns table alias.
	 * 
	 * @return string
	 */
	protected function alias() {
		if ( ! isset($this->alias))
			$this->alias = $this->create_alias();
		return $this->alias;
	}
	
	/**
	 * Generates new unique alias for current table.
	 * 
	 * @return string
	 */	
	protected function create_alias() {
		$tname = $this->table->name();
		if ( ! isset(self::$aliases[$tname]))
			self::$aliases[$tname] = 0;
		else
			self::$aliases[$tname] ++;
		return $tname . self::$aliases[$tname];
	}
	
	/**
	 * Returns SQL for given column.
	 *
	 * @param string $column
	 */
	public function __get($column) {
		if ( ! isset($this->columns_sql[$column]))
			$this->columns_sql[$column] = $this->create_column_sql($column);
		return $this->columns_sql[$column];
	}
	
	/**
	 * Creates SQL for given column.
	 *
	 * @param string $column
	 */	
	protected function create_column_sql($column) {
		$db		= $this->table->db();
		$col	= $this->table->column($column); 
		return $db->quote_identifier($this->alias) . '.' . $db->quote_identifier($col->dbcolumn());
	}
	
	/**
	 * Returns SQL for table and alias.
	 * 
	 * @return string
	 */
	public function table_sql() {
		if ( ! isset($this->table_sql))
			$this->table_sql = $this->create_table_sql();
		return $this->table_sql;
	}

	/**
	 * Creates SQL for table and alias.
	 *
	 * @param string $column
	 */	
	protected function create_table_sql() {
		$db = $this->table->db();
		return $db->quote_identifier($this->table->dbtable()) . ' AS ' . $db->quote_identifier($this->alias());
	}	
}