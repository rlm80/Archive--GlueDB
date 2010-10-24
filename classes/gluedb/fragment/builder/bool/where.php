<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build a where clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Builder_Bool_Where extends GlueDB_Fragment_Builder_Bool {
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