<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base fragment class.
 *
 * A fragment is a data structure that describes a piece of SQL query.
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
	 * @var array Cached compiled SQL. There is one entry by database.
	 */
	protected $sql;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment $parent
	 */
	public function __construct(GlueDB_Fragment $parent = null) {
		$this->parent = $parent;
	}

	/**
	 * Returns compiled SQL string.
	 *
	 * Calling this function repeatedly won't trigger the compiling process everytime,
	 * there is a cache that is only invalidated when the data structure is modified.
	 *
	 * @return string
	 */
	public function sql($dbname = GlueDB_Database::DEFAULTDB) {
		if ( ! isset($this->sql[$dbname]))
			$this->sql[$dbname] = $this->compile(gluedb::db($dbname));
		return $this->sql[$dbname];
	}

	/**
	 * Compiles the data structure against given database and returns the
	 * resulting SQL string.
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	abstract protected function compile($dbname);

	/**
	 * Clears the SQL cache and forwards call to context. Must be called each time
	 * a change has been made to the data structure.
	 */
	protected function invalidate() {
		// No need to do anything if fragment is already invalidated : (TODO make sure this is correct)
		if (count($this->sql) > 0) {
			// Reset SQL cache :
			$this->sql = array();

			// Cascade call to parent, because if a child is invalid, the parent necessarily is, too :
			if (isset($this->parent))
				$this->parent->invalidate();
		}
	}
}