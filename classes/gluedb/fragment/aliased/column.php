<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a column - alias pair in a select list.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Aliased_Column extends GlueDB_Fragment_Aliased {
	/**
	 * @var GlueDB_Fragment_Column Column.
	 */
	protected $column;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment_Column $column
	 * @param string $alias
	 */
	public function __construct(GlueDB_Fragment_Column $column, $alias) {
		parent::__construct($alias);
		$this->column = $column;
		$this->column->register_user($this);
	}

	/**
	 * Generates unique alias.
	 *
	 * @return string
	 */
	protected function create_alias() {
		throw new Kohana_Exception("Should never happen !!! We always set a default alias for this.");
	}

	/**
	 * Column fragment getter.
	 *
	 * @return GlueDB_Fragment_Column
	 */
	public function column() {
		return $this->column;
	}

	/**
	 * Returns SQL string for everything that must come before the "AS".
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	protected function compile_definition($dbname) {
		return $this->column->sql($dbname);
	}
}