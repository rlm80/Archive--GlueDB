<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build an order by clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Builder_Orderby extends GlueDB_Fragment_Builder {
	/**
	 * Adds an element at the end of the order by. You may pass any fragment, or a string template
	 * with question marks as placeholders, followed by their replacement values or fragments.
	 *
	 * @return GlueDB_Fragment_Ordered
	 */
	public function then() {
		// Get params :
		$params	= func_get_args();

		// Split params :
		$first = array_shift($params);

		// Build fragment :
		if ($first instanceof GlueDB_Fragment)
			$fragment = new GlueDB_Fragment_Ordered($first);
		else
			$fragment = new GlueDB_Fragment_Ordered(new GlueDB_Fragment_Template($first, $params));

		// Add fragment :
		$this->push($fragment);

		// Return fragment :
		return $fragment;
	}
}