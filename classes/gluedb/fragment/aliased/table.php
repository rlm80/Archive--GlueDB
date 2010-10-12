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
	 * @var GlueDB_Table Table.
	 */
	protected $table;

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
	public function __construct($table_name, $alias) {
		$this->table = gluedb::table($table_name);
		$this->alias = $alias;
	}

	/**
	 * Generates unique alias for given table.
	 *
	 * @param string $table_name
	 *
	 * @return string
	 */
	static public function create_alias($table_name) {
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
			$this->columns[$column] = new GlueDB_Fragment_Column($this, $this->table->column($column));
		return $this->columns[$column];
	}

	/**
	 * Returns SQL string for everything that must come before the "AS".
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	protected function compile_definition($dbname) {
		$db	= gluedb::db($dbname);
		return $db->compile_identifier($this->table->dbtable());
	}
}