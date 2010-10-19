<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Provides basic functionality for fragments that have children fragments and
 * that compile into an SQL string that is simply the concatenation of the SQL
 * strings of each child fragment, separated with a connector.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Builder extends GlueDB_Fragment {
	/**
	 * @var array List of children fragments.
	 */
	protected $children = array();

	/**
	 * Adds a child at the end of the children list.
	 *
	 * @param GlueDB_Fragment $fragment
	 */
	protected function push(GlueDB_Fragment $fragment) {
		$this->children[] = $fragment;
		$fragment->register_user($this);
		$this->invalidate();
	}

	/**
	 * Returns last fragment pushed, or null if there is no such fragment.
	 *
	 * @return GlueDB_Fragment
	 */
	protected function last() {
		if ($count = count($this->children))
			return $this->children[$count - 1];
		else
			return null;
	}

	/**
	 * Removes the last child at the end of the children list.
	 *
	 * @return GlueDB_Fragment_Builder
	 */
	public function pop() {
		$fragment = array_pop($this->children);
		$fragment->unregister_user($this);
		$this->invalidate();
		return $this;
	}

	/**
	 * Removes all children.
	 *
	 * @return GlueDB_Fragment_Builder
	 */
	public function reset() {
		while ($fragment = array_pop($this->children))
			$fragment->unregister_user($this);
		$this->invalidate();
		return $this;
	}

	/**
	 * Whether or not children list is empty.
	 *
	 * @return boolean
	 */
	public function is_empty() { // TODO remove this. children() function is enough
		return count($this->children) === 0;
	}

	/**
	 * Returns children fragments.
	 *
 	 * @return array
	 */
	public function children() {
		return $this->children;
	}
}