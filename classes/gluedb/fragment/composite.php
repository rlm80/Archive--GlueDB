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
	 * Adds a child at the end of the children list. Protected, because children classes will probably
	 * want to warp this in a function with a more meaningful name.
	 *
	 * @param GlueDB_Fragment $child
	 */
	protected function push($child) {
		$this->children[] = $child;
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
	 * Compiles the data structure against given database and returns the
	 * resulting SQL string. In this case, the resulting SQL string is simply
 	 * the concatenation of the resulting SQL strings of the children fragments.
	 *
	 * TODO add '(' . .... . ')' ???
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