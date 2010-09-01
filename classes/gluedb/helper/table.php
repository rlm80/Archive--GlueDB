<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Table helper class.
 *
 * A table helper is an object that helps in modifying the parts of a query
 * that relate to a specific table instance in that query. It also returns
 * columns helpers.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Helper_Table extends GlueDB_Helper {
	/**
	 * @var GlueDB_Table The table that this helper is about.
	 */
	protected $table;

	/**
	 * @var string Alias of table in the context query of this helper.
	 */
	protected $alias;

	/**
	 * @var array Column helpers cache.
	 */
	protected $column_helpers = array();

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Query $query
	 * @param GlueDB_Table $table
	 * @param string $alias
	 */
	public function __construct(GlueDB_Query $query, GlueDB_Table $table, $alias) {
		parent::__construct($query);
		$this->table = $table;
		$this->alias = $alias;
	}


	/**
	 * Returns boolean builder for ON clause of the table.
	 *
	 * @return GlueDB_Builder_Boolean
	 */
	public function on() {
		return $todo;
	}

	/**
	 * Returns the column helper for given column.
	 *
	 * @param string $column
	 */
	public function __get($column) {
		if ( ! isset($this->column_helpers[$column]))
			$this->column_helpers[$column] = new GlueDB_Helper_Column($this->query, $this->table, $this->alias, $column, $this);
		return $this->column_helpers[$column];
	}
}