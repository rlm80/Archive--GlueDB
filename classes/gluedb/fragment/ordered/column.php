<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a column in an order by clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Ordered_Column extends GlueDB_Fragment_Ordered {
	/**
	 * @var GlueDB_Fragment_Column Column.
	 */
	protected $column;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment_Column $column
	 */
	public function __construct(GlueDB_Fragment_Column $column) {
		$this->column = $column;
		$this->column->register_user($this);
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
	 * Returns SQL string for everything that must come before the " ASC"/" DESC".
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	protected function compile_definition($dbname) {
		return $this->column->sql($dbname);
	}
}