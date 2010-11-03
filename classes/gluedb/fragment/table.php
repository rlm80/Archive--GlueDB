<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a table.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Table extends GlueDB_Fragment {
	/**
	 * @var GlueDB_Table Table.
	 */
	protected $table;

	/**
	 * Constructor.
	 *
	 * @param string $table_name
	 */
	public function __construct($table_name) {
		$this->table = gluedb::table($table_name);
	}

	/**
	 * Table setter/getter.
	 *
	 * @return mixed
	 */
	public function table($table_name = null) {
		if (func_num_args() === 0)
			return $this->table;
		else {
			$table = gluedb::table($table_name);
			return $this->set_property('table', $table);
		}
	}
}