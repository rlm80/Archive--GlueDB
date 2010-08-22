<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * ANSI dialect class.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Dialect_ANSI extends GlueDB_Dialect {
	/**
	 * Escapes a string according to ANSI conventions.
	 *
	 * @param string $string
	 *
	 * @return
	 */
	public function quote($string) {
		return addslashes($string);
	}

	/**
	 * Quotes an identifier according to ANSI conventions.
	 *
	 * @param string $identifier
	 *
	 * @return
	 */
	public function quote_identifier($identifier) {
		return '"' . $identifier . '"';
	}

	/**
	 * Returns the appropriate PHP type to represent given ANSI type.
	 *
	 * @param string $dbtype
	 *
	 * @return string
	 */
	public function get_phptype($dbtype) {
		return 'string'; // TODO But is this really necessary ? It will be redefined entirely
						 // in every children classes anyway.
	}

	/**
	 * Compiles a datastructure representing an SQL query into an SQL string
	 * according to ANSI conventions.
	 *
	 * @param mixed $statement
	 *
	 * @return string
	 */
	public function compile($statement) {
		if (is_string($statement))
			return $statement;
		elseif (true) {
			// TODO
		}
		else
			return parent::compile($statement);
	}
}