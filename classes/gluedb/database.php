<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base database class.
 * 
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Database extends PDO {
	/**
	 * @var array Database instances cache.	
	 */
	static protected $instances = array();
	
	/**
	 * Lazy loads a database object, stores it in cache, and returns it.
	 * 
	 * @param string $name
	 * 
	 * @return object
	 */
	static public function get($name) {
		$name = strtolower($name);
		if( ! isset(self::$instances[$name]))
			self::$instances[$name] = self::build($name);
		return self::$instances[$name];
	}
	
	/**
	 * Returns a new database instance. Throws class not found exception if
	 * no class is defined for given database.
	 * 
	 * @param string $name
	 * 
	 * @return object
	 */
	static protected function build($name) {
		$class = 'GlueDB_Database_'.ucfirst($name);
		return new $class;
	}
}