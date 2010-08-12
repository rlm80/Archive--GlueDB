<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base MySQL database class.
 *
 * @package    GlueDB
 * @author     R�gis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Table {
	/**
	 * @var array Tables instances cache.
	 */
	static protected $instances = array();

	/**
	 * @var string Identifier of the database this table is stored into.
	 */
	protected $dbname;

	/**
	 * @var string Name of this table.
	 */
	protected $name;

	/**
	 * Constructor.
	 *
	 * @param string $name
	 */
	protected function __construct($dbname, $name) {
		// Set database identifier :
		$this->dbname = $dbname;

		// Set table name :
		$this->name = $name;
	}

	/**
	 * Returns a select update object for current table.
	 *
	 * @return GlueDB_Query_Update
	 */
	public function update() {
		return new GlueDB_Query_Update($this);
	}

	/**
	 * Returns a select delete object for current table.
	 *
	 * @return GlueDB_Query_Delete
	 */
	public function delete() {
		return new GlueDB_Query_Delete($this);
	}

	/**
	 * Returns a select insert object for current table.
	 *
	 * @return GlueDB_Query_Insert
	 */
	public function insert() {
		return new GlueDB_Query_Insert($this);
	}

	/**
	 * Lazy loads a table object, stores it in cache, and returns it.
	 *
	 * @param string $dbname	Database name.
	 * @param string $name		Table name.
	 *
	 * @return GlueDB_Table
	 */
	static public function get($dbname, $name) {
		$dbname	= strtolower($dbname);
		$name	= strtolower($name);
		if( ! isset(self::$instances[$dbname][$name]))
			self::$instances[$dbname][$name] = self::create($dbname, $name);
		return self::$instances[$dbname][$name];
	}

	/**
	 * Returns a new table instance. Throws class not found exception if
	 * no class is defined for given table.
	 *
	 * @param string $name
	 *
	 * @return object
	 */
	static protected function create($dbname, $name) {
		$class = 'GlueDB_Table_' . ucfirst($dbname) . '_' . ucfirst($name);
		return new $class($dbname, $name);
	}
}
