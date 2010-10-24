<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build an group by clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Builder_List_Groupby extends GlueDB_Fragment_Builder_List {
	/**
	 * @param GlueDB_Fragment_Query $query
	 */
	public function __construct(GlueDB_Fragment_Query $query = null) {
		if (isset($query)) {
			$this->set_forward($query);
			$this->register_user($query);
		}
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
}