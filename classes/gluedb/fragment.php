<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base fragment class.
 *
 * A fragment is a data structure that describes a piece of SQL query and generates
 * the corresponding SQL string.
 *
 * Fragments may rely on other fragments to build their SQL representations. Such dependencies
 * between fragments constitute an acyclic directed graph. Fragments that depend on a specific
 * fragment in such a way are called the users of that fragment.
 *
 * Fragments cache their SQL representations. When the data structure that makes up a fragment
 * changes, the cache is invalidated. This cache invalidation process is cascaded recursively
 * from the fragment to its users and the users of its users and so on.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Fragment {
	/**
	 * @var array List of fragments that make direct use of this fragment to create their own SQL representation.
	 */
	protected $users;

	/**
	 * @var array Cached compiled SQL strings. One entry for each database.
	 */
	protected $sql = array();

	/**
	 * Returns compiled SQL string for given database.
	 *
	 * Calling this function repeatedly won't trigger the compiling process everytime,
	 * there is a cache that is only invalidated when the data structure is modified.
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	public function sql($dbname = GlueDB_Database::DEFAULTDB) {
		if ( ! isset($this->sql[$dbname]))
			$this->sql[$dbname] = $this->compile($dbname);
		return $this->sql[$dbname];
	}

	/**
	 * Compiles the data structure and returns the resulting SQL string.
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	abstract protected function compile($dbname);

	/**
	 * Adds a fragment to the list of fragments that make direct use of this
	 * fragment to create their own SQL representation (a bit more complicated than
	 * it seems because a user may be added more than once and we have to keep
	 * track of that to remove it properly).
	 *
	 * @param GlueDB_Fragment $user
	 */
	protected function register_user(GlueDB_Fragment $user) {
		$hash = spl_object_hash($user);
		if ( ! isset($this->users[$hash]))
			$this->users[$hash] = array('object' => $user, 'count' => 1);
		else
			$this->users[$hash]['count'] ++;
	}

	/**
	 * Removes a fragment from the list of fragments that make direct use of this
	 * fragment to create their own SQL representation (a bit more complicated than
	 * it seems because a user may be removed more than once).
	 *
	 * @param GlueDB_Fragment $user
	 */
	protected function unregister_user(GlueDB_Fragment $user) {
		$hash = spl_object_hash($user);
		$this->users[$hash]['count'] --;
		if ($this->users[$hash]['count'] === 0)
			unset($this->users[$hash]);
	}

	/**
	 * Clears the SQL cache and forwards call to users. Must be called each time
	 * a change has been made to the data structure.
	 */
	protected function invalidate() {
		// No need to do anything if fragment is already invalidated :
		if (count($this->sql) !== 0) {
			// Reset SQL cache :
			$this->sql = array();

			// Cascade call to parent, because if a child is invalid, the parent is necessarily invalid too :
			foreach($this->users as $user)
				$user['object']->invalidate();
		}
	}
}














