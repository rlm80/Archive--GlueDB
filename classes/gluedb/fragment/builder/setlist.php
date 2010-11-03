<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build the set list in an update query.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Builder_Setlist extends GlueDB_Fragment_Builder {
	/**
	 * Adds an element at the end of the set list
	 *
	 * @param GlueDB_Fragment_Column $column
	 * @param mixed $to
	 *
	 * @return GlueDB_Fragment_Assignment
	 */
	public function _and($column, $to = null) {
		// Build fragment :
		$fragment = new GlueDB_Fragment_Assignment($column, $to);

		// Add fragment :
		$this->push($fragment);

		// Return fragment :
		return $fragment;
	}
}