<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a column of a specific table - alias pair and compiles into
 * a "<table_alias>.<column_name>" SQL string.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Column extends GlueDB_Fragment {
	/**
	 * @var GlueDB_Fragment_Aliased_Table
	 */
	protected $table_alias;

	/**
	 * @var string Column.
	 */
	protected $column;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment_Aliased_Table $table_alias
	 * @param string $column Column name.
	 */
	public function __construct(GlueDB_Fragment_Aliased_Table $table_alias, $column) {
		$this->table_alias = $table_alias;
		$this->column = $column;
		$table_alias->register_user($this);
	}

	/**
	 * Column getter.
	 *
	 * @return GlueDB_Column
	 */
	public function column() {
		return $this->table_alias->table()->column($this->column);
	}

	/**
	 * Compiles the data structure and returns the resulting SQL string.
	 *
	 * @param GlueDB_Database $db
	 *
	 * @return string
	 */
	protected function compile(GlueDB_Database $db) {
		$tablesql	= $db->compile_identifier($this->table_alias->alias());
		$columnsql	= $db->compile_identifier($this->column()->dbcolumn());
		return $tablesql . '.' . $columnsql;
	}
	
	public function __toString() {
		return $this->sql($this->column()->table()->db());
	}
}