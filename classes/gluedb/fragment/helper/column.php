<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Column helper class.
 *
 * A column helper is an object that helps generating the parts of a query
 * that relate to a specific column of a table instance in that query, namely
 * aliases and expressions.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Helper_Column extends GlueDB_Fragment_Helper {
	/**
	 * @var GlueDB_Table The table this helper is about.
	 */
	protected $table;

	/**
	 * @var string Alias of table in the context query of this helper.
	 */
	protected $alias;

	/**
	 * @var string The column this helper is about.
	 */
	protected $column;

	/**
	 * @var GlueDB_Helper_Table Table helper that spawned this column helper.
	 */
	protected $table_helper;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Query $query
	 * @param GlueDB_Table $table
	 * @param string $alias
	 * @param string $column
	 * @param GlueDB_Table_Helper $table_helper
	 */
	public function __construct(GlueDB_Query $query, GlueDB_Table $table, $alias, $column, $table_helper) {
		parent::__construct($query);
		$this->table = $table;
		$this->alias = $alias;
		$this->column = $column;
		$this->table_helper = $table_helper;
	}
}