<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build a group by clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Builder_Groupby extends GlueDB_Fragment_Builder {
	/**
	 * Adds an element at the end of the group by. You may pass any fragment, or a string template
	 * with question marks as placeholders, followed by their replacement values or fragments.
	 *
	 * @return GlueDB_Fragment_Builder_Groupby
	 */
	public function then() {
		// Get params :
		$params	= func_get_args();

		// Split params :
		$first = array_shift($params);

		// Add fragment :
		if ($first instanceof GlueDB_Fragment)
			$this->push($first);
		else
			$this->push(new GlueDB_Fragment_Template($first, $params));

		return $this;
	}
}