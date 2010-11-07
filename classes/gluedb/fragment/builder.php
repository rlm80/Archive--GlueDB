<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Builders are fragments that provide a fluent API to build an assembly of children fragments.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Fragment_Builder extends GlueDB_Fragment {
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
	 * Returns last fragment pushed, or false if there is no such fragment.
	 *
	 * @return GlueDB_Fragment
	 */
	public function last() {
		return end($this->children);
	}

	/**
	 * Returns first fragment pushed, or false if there is no such fragment.
	 *
	 * @return GlueDB_Fragment
	 */
	public function first() {
		return reset($this->children);
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
		while (count($this->children) > 0)
			$this->pop();
		return $this;
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