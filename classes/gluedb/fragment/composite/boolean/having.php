<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build a having clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Composite_Boolean_Having extends GlueDB_Fragment_Composite_Boolean {
	/**
	 * @var Query that owns this having clause.
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
	 * @param string $name
	 * @param string $args
	 *
	 * @return mixed
	 */
	public function __call($name, $args) {
		$return = parent::__call($name, $args);
		if ( ! isset($return))
			return call_user_func_array(array($this->query, $name), $args);
		else
			return $return;
	}
}