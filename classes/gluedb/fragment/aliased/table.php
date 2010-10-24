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
	 * Table getter/setter.
	 * 
	 * @param string $table_name
	 * 
	 * @return mixed 
	 */
	public function table($table_name = null) {
		if (func_num_args() === 0)
			return $this->fragment->table();
		else {
			return $this->fragment(new GlueDB_Fragment_Table($table_name));
		}			
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