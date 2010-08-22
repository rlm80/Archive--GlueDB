<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
* Main GlueDB class. Contains only static methods. All of the GlueDB features can be
* accessed through this interface. Whatever you do with GlueDB, this should almost
* always be your entry point.
*
* @package GlueDB
* @author R�gis Lemaigre
* @license MIT
*/

class GlueDB {
	/**
	 * Returns the database object identified by $name.
	 *
	 * Databases are classes that behave like singletons. Calling this function once will create and
	 * return an instance of the class GlueDB_Database_$name. Subsequent calls to this function with
	 * the same parameter will return the same database instance.
	 *
	 * @param string $name
	 *
	 * @return GlueDB_Database
	 */
	public static function db($name = GlueDB_Database::DEFAULTDB) {
		return GlueDB_Database::get($name);
	}
}