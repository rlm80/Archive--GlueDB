<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base fragment class.
 *
 * A fragment is a data structure that describes a piece of SQL query and generates
 * the corresponding SQL string.
 *
 * More precisely, a fragment is any object able to generate an SQL string by combining its own
 * internal data with the SQL strings generated by other fragments. Such dependencies between
 * fragments constitute an acyclic directed graph. Fragments that depend on a specific fragment
 * in such a way are called the users of that fragment.
 *
 * Fragments cache their SQL representations. When their internal data is modified, the cache is
 * invalidated. This cache invalidation process is cascaded recursively from the fragment to its
 * users and the users of its users and so on.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Fragment {
	/**
	 * @var integer Default SQL style.
	 */
	const STYLE_DEFAULT = -1;
	
	/**
	 * @var array List of fragments that make direct use of this fragment to create their own SQL representation.
	 */
	protected $users;

	/**
	 * @var array Cached compiled SQL strings. One entry for each database.
	 */
	protected $sql = array();

	/**
	 * Returns compiled SQL string according to given database SQL dialect.
	 *
	 * @param GlueDB_Database $db Database that defines what SQL dialect must be used to compile the fragment.
	 * @param integer $style 
	 *
	 * @return string
	 */
	public function sql(GlueDB_Database $db = null, $style = self::STYLE_DEFAULT) {
		// No database given ? Means default database :
		if ( ! isset($db))
			$db = gluedb::db(GlueDB_Database::DEFAULTDB);

		// Get name of given database instance :
		$dbname = $db->name();

		// Retrieve SQL from cache, or create it and add it to cache if it isn't there yet :
		if ( ! isset($this->sql[$dbname][$style]))
			$this->sql[$dbname][$style] = $this->compile($db, $style);

		// Return SQL :
		return $this->sql[$dbname][$style];
	}

	/**
	 * Returns freshly compiled (i.e. not retrieved from cache) SQL string, according
	 * to given database SQL dialect.
	 *
	 * @param GlueDB_Database $db
	 * @param integer $style
	 *
	 * @return string
	 */
	protected function compile(GlueDB_Database $db, $style) {
		// Forwards call to database :
		return $db->compile($this, $style);
	}

	/**
	 * Adds a fragment to the list of fragments that make direct use of this
	 * fragment to create their own SQL representation.
	 *
	 * @param GlueDB_Fragment $user
	 */
	protected function register_user(GlueDB_Fragment $user) {
		$this->users[] = $user;
	}

	/**
	 * Removes a fragment from the list of fragments that make direct use of this
	 * fragment to create their own SQL representation.
	 *
	 * @param GlueDB_Fragment $user
	 */
	protected function unregister_user(GlueDB_Fragment $user) {
		foreach (array_reverse($this->users, true) as $i => $u) {
			if ($u === $user) {
				unset($this->users[$i]);
				break;
			}
		}
	}

	/**
	 * Clears the SQL cache and forwards call to users. Must be called each time
	 * a change has been made to this fragment that may change its SQL.
	 */
	protected function invalidate() {
		// No need to do anything if fragment is already invalidated :
		if (count($this->sql) !== 0) {
			// Reset SQL cache :
			$this->sql = array();

			// Cascade call to parent, because if a child is invalid, the parent is necessarily invalid too :
			foreach($this->users as $user)
				$user->invalidate();
		}
	}

	/**
	 * Helper function that sets the value of a property.
	 *
	 * @param string $prop
	 * @param mixed $value
	 *
	 * @return GlueDB_Fragment
	 */
	protected function set_property($prop, $value) {
		if ($this->$prop !== $value) {
			// If old value of property is a fragment, we must remove the dependency :
			if (isset($this->$prop) && $this->$prop instanceof GlueDB_Fragment)
				$this->$prop->unregister_user($this);

			// If new value of property is a fragment, we must set up the dependency :
			if (isset($value) && $value instanceof GlueDB_Fragment)
				$value->register_user($this);

			// Set new value of property :
			$this->$prop = $value;

			// Invalidate fragment :
			$this->invalidate();
		}
		return $this;
	}

	/**
	 * Returns the context, that is, the last parent fragment this fragment was attached to.
	 *
	 * @return GlueDB_Fragment
	 */
	public function context() {
		if (count($this->users) > 0)
			return end($this->users);
		else
			return null;
	}

	/**
	 * Returns the top-level context.
	 *
	 * @return GlueDB_Fragment
	 */
	public function root() {
		$context = $this->context();
		if ( ! isset($context))
			return $this;
		else
			return $context->root();
	}

	/**
	 * Throws an exception if there is no context to forward a function call, returns
	 * current context otherwise.
	 *
	 * @return GlueDB_Fragment
	 */
	protected function check_forwarding($function) {
		$context = $this->context();
		if ( ! isset($context))
			throw new Kohana_Exception("Cannot call function '" . $function . "' in this context.");
		else
			return $context;
	}

	/*
	 * Sets up aliases for _or(), _and() and _as(). Required because
	 * keywords aren't valid function names in PHP. Also forwards
	 * unknown calls to context.
	 */
	public function __call($name, $args) {
		if ($name === 'or')
			return call_user_func_array(array($this, '_or'), $args);
		elseif ($name === 'and')
			return call_user_func_array(array($this, '_and'), $args);
		elseif ($name === 'as')
			return call_user_func_array(array($this, '_as'), $args);
		else {
			$context = $this->check_forwarding($name);
			return call_user_func_array(array($context, $name), $args);
		}
	}

	// Following functions cannot be forwarded with __call because __call doesn't support reference arguments.
	public function left($arg1,  &$arg2 = null) { return $this->check_forwarding(__METHOD__)->left($arg1, $arg2); }
	public function right($arg1, &$arg2 = null) { return $this->check_forwarding(__METHOD__)->right($arg1, $arg2); }
	public function inner($arg1, &$arg2 = null) { return $this->check_forwarding(__METHOD__)->inner($arg1, $arg2); }
}














