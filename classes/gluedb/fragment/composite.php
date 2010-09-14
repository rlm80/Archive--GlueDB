<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Provides basic functionality for fragments that have children fragments and
 * that compile into an SQL string that is simply the concatenation of the SQL
 * strings of each child fragment.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Composite extends GlueDB_Fragment {
	/**
	 * @var array List of children fragments.
	 */
	protected $children = array();

	/**
	 * Adds a child, or an array of children, at the end of the children list.
	 *
	 * If the parameter is an array of children, they will be added as a composite fragment that
	 * can be removed with pop() in one call.
	 *
	 * @param mixed $fragments
	 */
	protected function push($fragments) {
		// Add children :
		if (is_array($fragments)) {
			// Recursion (fragments is an array) :
			$atomic = new GlueDB_Fragment_Composite();
			foreach($fragments as $fragment)
				$atomic->push($fragment);
			$this->push($atomic);
		}
		else {
			// Trivial case (fragments is a single element) :
			$this->children[] = $fragments;
			$fragments->set_parent($this);
		}

		// Invalidate :
		$this->invalidate();
	}

	/**
	 * Removes the last child at the end of the children list.
	 *
	 * @return GlueDB_Fragment_Composite
	 */
	public function pop() {
		array_pop($this->children);
		$this->invalidate();
		return $this;
	}

	/**
	 * Whether or not children list is empty.
	 *
	 * @return boolean
	 */
	public function is_empty() {
		return count($this->children) === 0;
	}

	/**
	 * Removes all children.
	 *
	 * @return GlueDB_Fragment_Composite
	 */
	public function reset() {
		$this->children = array();
		$this->invalidate();
		return $this;
	}

	/**
	 * Compiles the data structure and returns the resulting SQL string.
	 *
	 * In this case the resulting SQL string is simply the concatenation of the resulting
	 * SQL strings of the children fragments.
	 *
	 * @return string
	 */
	protected function compile() {
		$sql = '';
		foreach ($this->children as $child)
			$sql .= $child->sql();
		return $sql;
	}
}