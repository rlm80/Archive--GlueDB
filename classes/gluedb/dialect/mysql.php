<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * MySQL dialect class.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Dialect_MySQL extends GlueDB_Dialect_ANSI {
	/**
	 * Escapes a string according to MySQL conventions.
	 *
	 * @param string $string
	 *
	 * @return
	 */
	public function quote($string) {
		return mysql_real_escape_string($string);
	}
	
	/**
	 * Quotes an identifier according to MySQL conventions.
	 * Mysql uses back-ticks for this instead of the ANSI double quote standard character.
	 *
	 * @param string $identifier
	 *
	 * @return
	 */
	public function quote_identifier($identifier) {
		return '`' . $identifier . '`';
	}
}