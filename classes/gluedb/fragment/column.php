<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a column definition in a select list.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Fragment_Column extends GlueDB_Fragment {
	/**
	 * @var string Column alias.
	 */
	protected $alias;

	/**
	 * Returns column alias.
	 */
	abstract protected function alias();

	/**
	 * Alias setter.
	 *
	 * @param string $alias
	 */
	protected function set_alias($alias) {
		$this->alias = $alias;
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
		$def_sql	= $this->compile_definition($dbname);
		$alias_sql	= $db->quote_identifier($this->alias());
		return $db->compile_column($def_sql, $alias_sql);
	}

	/**
	 * Returns column definition SQL.
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	abstract function compile_definition($dbname);
}