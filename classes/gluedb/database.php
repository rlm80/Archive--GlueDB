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
	
	/*
	 * Same as PDO::query if statement is a string. If statement is a data structure
	 * representing an SQL query, it is first compiled into an SQL string before being
	 * handed over to the PDO corresponding method.
	 * 
	 * @see PDO::query()
	 */
	public function query($statement) {
		$sql = $this->compile($statement);
		return parent::query($sql);
	}
	
	/*
	 * Same as PDO::prepare if statement is a string. If statement is a data structure
	 * representing an SQL query, it is first compiled into an SQL string before being
	 * handed over to the PDO corresponding method.
	 * 
	 * @see PDO::prepare()
	 */
	public function prepare($statement, $driver_options) {
		$sql = $this->compile($statement);
		return parent::query($sql);		
	}
	
	/*
	 * Same as PDO::exec if statement is a string. If statement is a data structure
	 * representing an SQL query, it is first compiled into an SQL string before being
	 * handed over to the PDO corresponding method.
	 * 
	 * @see PDO::exec()
	 */
	public function exec($statement) {
		$sql = $this->compile($statement);
		return parent::query($sql);		
	}
	
	/**
	 * Compiles a datastructure representing an SQL query into an SQL string.
	 * 
	 * @param mixed $statement
	 * 
	 * @return string
	 */
	protected function compile($statement) {
		if (is_string($statement))
			return $statement;
		else {
			// This is where the magic happens.
		}
	}
	
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