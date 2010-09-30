<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build an order by clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Composite_Orderby extends GlueDB_Fragment_Composite_List {
	/**
	 * @var Query that owns this order by clause.
	 */
	protected $query;

	/**
	 * @param GlueDB_Query $query
	 */
	public function __construct(GlueDB_Query $query) {
		$this->query = $query;
	}

	/**
	 * Forwards unknown calls to query.
	 *
	 * @param unknown_type $name
	 * @param unknown_type $args
	 *
	 * @return mixed
	 */
	public function __call($name, $args) {
		return call_user_func_array(array($this->query, $name), $args);
	}
}