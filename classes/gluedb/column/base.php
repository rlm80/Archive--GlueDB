<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base virtual column class.
 *
 * Columns may be computed or map to a real column in a real table.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Column_Base {
	/**
	 * @var GlueDB_Table_Base Virtual table object this column belongs to.
	 */
	protected $table;

	/**
	 * @var string Name of this column, as you would refer to it in queries.
	 */
	protected $name;

	/**
	 * @var string PHP type of the values returned by this column.
	 */
	protected $type;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Table_Base $table
	 * @param string $name
	 * @param string $type
	 */
	public function __construct(GlueDB_Table_Base $table, $name, $type) {
		$this->table = $table;
		$this->name = $name;
		$this->type = $type;
	}
}
