<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base fragment class.
 *
 * A fragment is a data structure that describes a piece of SQL query and generates
 * the corresponding SQL string.
 *
 * Fragments are arranged as a tree, each fragment belonging to a parent fragment. At the top of
 * this tree is the query. Each fragment in the tree compiles into a string that is the SQL
 * representation of that fragment and the whole subtree that lives under it.
 *
 * Fragments cache their compiled SQL representations. When a change occurs to a fragment, these
 * caches are invalidated from child to parent all the way up to the root of the tree.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Fragment {
	/**
	 * @var GlueDB_Fragment Fragment of which the current fragment is a piece of.
	 */
	protected $parent;

	/**
	 * @var string Cached compiled SQL.
	 */
	protected $sql;

	/**
	 * Returns compiled SQL string.
	 *
	 * Calling this function repeatedly won't trigger the compiling process everytime,
	 * there is a cache that is only invalidated when the data structure is modified.
	 *
	 * @return string
	 */
	public function sql() {
		if ( ! isset($this->sql))
			$this->sql = $this->compile();
		return $this->sql;
	}

	/**
	 * Compiles the data structure and returns the resulting SQL string.
	 *
	 * @return string
	 */
	abstract protected function compile();

	/**
	 * Sets parent of fragment.
	 *
	 * @param GlueDB_Fragment $parent
	 */
	protected function set_parent(GlueDB_Fragment $parent) {
		$this->parent = $parent;
	}

	/**
	 * Clears the SQL cache and forwards call to context. Must be called each time
	 * a change has been made to the data structure.
	 */
	protected function invalidate() {
		// No need to do anything if fragment is already invalidated :
		if (isset($this->sql)) {
			// Reset SQL cache :
			$this->sql = null;

			// Cascade call to parent, because if a child is invalid, the parent is necessarily invalid too :
			if (isset($this->parent))
				$this->parent->invalidate();
		}
	}

	/**
	 * Returns the query at the root of the fragment tree.
	 *
	 * @return GlueDB_Fragment_Query
	 */
	protected function root() {
		if (isset($this->parent))
			return $this->parent->root();
		else
			return $this;
	}
}














