<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build an group by clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Composite_List_Groupby extends GlueDB_Fragment_Composite_List {
	/**
	 * @var GlueDB_Fragment_Query Query that owns this order by clause.
	 */
	protected $query;

	/**
	 * @param GlueDB_Fragment_Query $query
	 */
	public function __construct(GlueDB_Fragment_Query $query) {
		$this->query = $query;
	}

	/**
	 * Adds fragment of appropriate type.
	 *
	 * @param array $params
	 */
	protected function add($params) {
		// Split params :
		$first = array_shift($params);

		// Add fragment :
		if ($first instanceof GlueDB_Fragment_Column)
			$this->push($first);
		else
			$this->push(new GlueDB_Fragment_Template($first, $params));
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