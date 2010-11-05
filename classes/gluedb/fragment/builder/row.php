<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a row of values in an insert query.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Builder_Row extends GlueDB_Fragment_Builder {
	/**
	 * Constructor. You may pass an array of values, or an unlimited number of parameters.
	 */
	public function __construct() {
		// Get args :
		$args = func_get_args();
	}
}