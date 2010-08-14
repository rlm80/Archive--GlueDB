<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * PostgreSQL dialect class.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Dialect_PostgreSQL extends GlueDB_Dialect_ANSI {
	/**
	 * Escapes a string according to PostgreSQL conventions.
	 *
	 * @param string $string
	 *
	 * @return
	 */
	public function quote($string) {
		return pg_escape_string($string);
	}
}