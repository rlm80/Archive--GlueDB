<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that is a sequence of other fragments. The resulting SQL string is simply
 * the concatenation of the resulting SQL strings of the children fragments.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Composite extends GlueDB_Fragment {
	/**
	 * @var array Components of current expression.
	 */
	protected $children = array();

	/**
	 * Adds a child at the end of the sequence.
	 *
	 * @param array $child
	 */
	protected function push($child) {
		// Add child :
		$this->children[] = $child;

		// Invalidate :
		$this->invalidate();
	}

	/**
	 * Compiles the data structure against given database and returns the
	 * resulting SQL string. In this case, the resulting SQL string is simply
 	 * the concatenation of the resulting SQL strings of the children fragments.
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	protected function compile($dbname) {
		$sql = '';
		foreach ($this->children as $child)
			$sql .= $child->sql($dbname);
		return $sql;
	}
}