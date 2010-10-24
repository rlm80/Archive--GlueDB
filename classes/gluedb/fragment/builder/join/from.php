<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build a from clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Builder_Join_From extends GlueDB_Fragment_Builder_Join {
	/**
	 * @param GlueDB_Fragment_Query $query
	 */
	public function __construct(GlueDB_Fragment_Query $query = null) {
		if (isset($query)) {
			$this->set_forward($query);
			$this->register_user($query);
		}
	}
}