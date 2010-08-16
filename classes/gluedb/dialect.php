<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base dialect class.
 *
 * A dialect object is capable of compiling data structures representing queries or
 * fragments of queries into SQL strings according to a given SQL dialect.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Dialect {
	/**
	 * Escapes a string according to current SQL dialect conventions.
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	abstract public function quote($string);

	/**
	 * Quotes an identifier according to current SQL dialect conventions.
	 *
	 * @param string $identifier
	 *
	 * @return string
	 */
	abstract public function quote_identifier($identifier);

	/**
	 * Returns the appropriate PHP type to represent given native database type.
	 *
	 * @param string $dbtype
	 *
	 * @return string
	 */
	abstract public function phptype($dbtype);

	/**
	 * Compiles a datastructure representing an SQL query into an SQL string
	 * according to current SQL dialect conventions.
	 *
	 * @param mixed $statement
	 *
	 * @return string
	 */
	public function compile($statement) {
		throw new Kohana_Exception("Unable to compile data structure " . $statement);
	}
}