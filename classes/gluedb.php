<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
* Main GlueDB class. Contains only static methods. All of the GlueDB features can be
* accessed through this interface. Whatever you do with GlueDB, this should almost
* always be your entry point.
*
* @package GlueDB
* @author Régis Lemaigre
* @license MIT
*/

class GlueDB {
	/**
	 * Returns a database instance.
	 *
	 * @param string $name
	 *
	 * @return GlueDB_Database
	 */
	public static function db($name = 'main') {
		return GlueDB_Database::get($name);
	}

	/**
	 * Returns a select query object.
	 *
	 * @return GlueDB_Query_Select
	 */
	public static function select() {
		return new GlueDB_Query_Select();
	}

	/**
	 * Returns a select update object.
	 *
	 * @return GlueDB_Query_Update
	 */
	public static function update() {
		return new GlueDB_Query_Update();
	}

	/**
	 * Returns a select delete object.
	 *
	 * @return GlueDB_Query_Delete
	 */
	public static function delete() {
		return new GlueDB_Query_Delete();
	}

	/**
	 * Returns a select insert object.
	 *
	 * @return GlueDB_Query_Insert
	 */
	public static function insert() {
		return new GlueDB_Query_Delete();
	}
}