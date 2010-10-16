<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a table - alias pair and compiles into a "<table> AS <alias>" SQL string.
 *
 * Also provides easy access to column fragments through the use of __get($column).
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Aliased_Table extends GlueDB_Fragment_Aliased {
	/**
	 * @var array Alias pool.
	 */
	static protected $aliases = array();

	/**
	 * @var array Column fragments cache.
	 */
	protected $columns = array();

	/**
	 * Constructor.
	 *
	 * @param string $table_name
	 * @param string $alias
	 */
	public function __construct($table_name, $alias = null) {
		parent::__construct(new GlueDB_Fragment_Table($table_name), $alias);
	}
	
	/**
	 * Returns the table object.
	 * 
	 * @return GlueDB_Table 
	 */
	public function table() {
		return $this->fragment->table();
	}
	
	/**
	 * Returns alias.
	 *
	 * @return string
	 */
	public function alias() {
		if ( ! isset($this->alias))
			$this->alias = $this->create_alias();
		return $this->alias;
	}

	/**
	 * Generates unique alias.
	 *
	 * @return string
	 */
	protected function create_alias() {
		$table_name = $this->table()->name();
		if ( ! isset(self::$aliases[$table_name]))
			self::$aliases[$table_name] = 0;
		else
			self::$aliases[$table_name] ++;
		return $table_name . '__' . self::$aliases[$table_name];
	}

	/**
	 * Returns children column fragments.
	 *
	 * @param string $column
	 *
	 * @return GlueDB_Fragment_Column
	 */
	public function __get($column) {
	    if ( ! isset($this->columns[$column]))
			$this->columns[$column] = new GlueDB_Fragment_Column($this, $column);
		return $this->columns[$column];
	}
}