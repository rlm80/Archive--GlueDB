<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a simple column definition in a select list.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Column_Simple extends GlueDB_Fragment_Column {
	/**
	 * @var GlueDB_Helper Table helper that owns the column.
	 */
	protected $helper;

	/**
	 * @var string Column.
	 */
	protected $column;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Helper $helper
	 * @param string $column
	 */
	public function __construct(GlueDB_Helper $helper, $column) {
		$this->helper = $helper;
		$this->column = $column;
	}

	/**
	 * Returns column alias.
	 */
	protected function alias() {
		if ( ! isset($this->alias))
			$this->alias = $this->helper->alias() . '__' . $column;
		return $this->alias;
	}

	/**
	 * Returns column definition SQL.
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	protected function compile_definition($dbname) {
		$db = gluedb::db($dbname);
		return $db->quote_identifier($this->helper->alias()) . '.' . $db->quote_identifier($column);
	}
}