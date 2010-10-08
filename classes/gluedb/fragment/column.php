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
	 * @var GlueDB_Fragment_Alias_Table Table fragment.
	 */
	protected $table_alias;

	/**
	 * @var GlueDB_Column Column.
	 */
	protected $column;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment_Alias_Table $table_name
	 * @param GlueDB_Column $alias
	 */
	public function __construct(GlueDB_Fragment_Alias_Table $table_alias, GlueDB_Column $column) {
		$this->table_alias = $table_alias;
		$this->column = $column;
		$table_alias->register_user($this);
	}

	/**
	 * Compiles the data structure against given database and returns the
	 * resulting SQL string.
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	protected function compile($dbname) {
		$db			= gluedb::db($dbname);
		$tablesql	= $db->compile_identifier($this->table_alias->alias());
		$columnsql	= $db->compile_identifier($this->column->dbcolumn());
		return $tablesql . '.' . $columnsql;
	}
}