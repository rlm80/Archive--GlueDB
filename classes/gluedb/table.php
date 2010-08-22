<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Virtual table class.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Table extends GlueDB_Table_Base {
	/**
	 * @var string	Name of the database that owns the real tables that are part of
	 * 				this virtual table definition.
	 */
	protected $db;
	
	/**
	 * @var string	Name of the database that owns the real tables that are part of
	 * 				this virtual table definition.
	 */
	protected $dbtable;	

	/**
	 * @var array Primary key columns of this table.
	 */
	protected $pk;

	/**
	 * Constructor.
	 *
	 * @param string $name Table name.
	 */
	protected function __construct($name) {
		// Call parent constructor :
		parent::__construct($name);
		
		// Init properties :
		if ( ! isset($this->dbtable))	$this->dbtable = $this->init_dbtable();
		if ( ! isset($this->db))		$this->db = $this->init_db();
		
		// Create columns :
		$this->columns = $this->init_columns();
		
		// Create pk :
		$this->pk = $this->init_pk();
	}

	/**
	 * Returns the real name of the underlying table.
	 * 
	 * @return array
	 */
	protected function init_dbtable() {
		return $this->name;
	}	
	
	/**
	 * Returns the name of the database that owns the underlying table.
	 * 
	 * @return string
	 */
	protected function init_db() {
		return GlueDB_Database::DEFAULTDB; // TODO Do something better than this. We should look into each
										   // available database and search for one that owns all the real tables.
	}	
	
	/**
	 * Returns the alias under which a real column will be known in PHP-land.
	 * 
	 * This alias defines how you may refer to the column in the query builder. You
	 * may redefine this if, for example, you wish to change the name of a real column
	 * without impacting the PHP application, or the other way around.
	 * 
	 * @param string $column_real_name
	 * 
	 * @return string
	 */
	protected function get_column_alias($column_real_name) {
		$column_alias = $column_real_name;
		return $column_alias;
	}
	
	/**
	 * Returns the appropriate formatter for given column.
	 * 
	 * You may want to redefine this if, for example, it's not possible for GlueDB to
	 * guess the right PHP type from the db type (sqlite ?) or because you want some
	 * funky formatting like serialization.
	 * 
	 * @param string $column_alias
	 * @param string $dbtype
	 * 
	 * @return GlueDB_Formatter
	 */
	protected function get_column_formatter($column_alias, $dbtype) {
		// Get PHP type :
		$phptype = $this->db()->get_phptype($dbtype);
		
		// Choose formatter depending on type :
		switch ($phptype) {
			case 'integer'; case 'int';
				$formatter = new GlueDB_Formatter_Integer;
				break;
			case 'float';
				$formatter = new GlueDB_Formatter_Float;
				break;
			case 'boolean';
				$formatter = new GlueDB_Formatter_Boolean;
				break;
			default;
				$formatter = new GlueDB_Formatter_String;
		}
		
		return $formatter;
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
	protected function init_columns() {
		$columns = array();
		$info_table = $this->db()->info_table($this->dbtable); 
		foreach ($info_table['columns'] as $info_column) {
			// Get column alias :
			$alias = $this->get_column_alias($info_column['column']);
			
			// Get column formatter :
			$formatter = $this->get_column_formatter($alias, $dbtype);			
			
			// Create column :
			$columns[$alias] = new GlueDB_Column(
					$this, 
					$alias,
					$formatter,
					$this->dbtable, 
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
	 * Returns the database object this virtual table is stored into.
	 *
	 * @return GlueDB_Database
	 */
	public function db() {
		return gluedb::db($this->db);
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
	 * Underlying database table names.
	 *
	 * @return array
	 */
	public function dbtables() {
		return array($this->dbtable);
	}	
}
