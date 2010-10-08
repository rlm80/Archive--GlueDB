<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents an item - alias pair in a select list.
 *
 * As far as the SQL generation is concerned, the item can be any fragment, but only
 * GlueDB_Fragment_Column will give you additional functionality like type casting of results.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Alias_Column extends GlueDB_Fragment_Alias {
	/**
	 * @var string Item.
	 */
	protected $item;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment $item
	 * @param string $alias
	 */
	public function __construct(GlueDB_Fragment $item, $alias) {
		parent::__construct($alias);
		$this->item	= $item;
		$this->item->register_user($this);
	}

	/**
	 * Returns SQL string for everything that must come before the "AS".
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	protected function compile_definition($dbname) {
		$db	= gluedb::db($dbname);
		return $this->item->sql($dbname);
	}
}