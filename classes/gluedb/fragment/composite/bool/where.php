<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build a where clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Composite_Bool_Where extends GlueDB_Fragment_Composite_Bool {
	/**
	 * @var GlueDB_Fragment_Query Query that owns this where clause.
	 */
	protected $query;

	/**
	 * @param GlueDB_Fragment_Query $query
	 */
	public function __construct(GlueDB_Fragment_Query $query) {
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