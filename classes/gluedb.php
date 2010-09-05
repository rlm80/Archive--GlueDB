<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Main GlueDB class.
 *
 * Contains only static methods. Whatever you do with GlueDB, this should always be your entry point.
 *
 * @package GlueDB
 * @author Régis Lemaigre
 * @license MIT
 */

class GlueDB {
	/**
	 * Returns the database object identified by $db_name.
	 *
	 * Subsequent calls to this function with the same parameter will return the same database
	 * instance, not create a new one.
	 *
	 * @param string $db_name
	 *
	 * @return GlueDB_Database
	 */
	public static function db($db_name = GlueDB_Database::DEFAULTDB) {
		return GlueDB_Database::get($db_name);
	}

	/**
	 * Returns the virtual table identified by $table_name.
	 *
	 * Subsequent calls to this function with the same parameter will return the same
	 * virtual table instance, not create a new one.
	 *
	 * @param string $table_name
	 *
	 * @return GlueDB_Table_Base
	 */
	public static function table($table_name) {
		return GlueDB_Table_Base::get($table_name);
	}

	/**
	 * Returns a select query object.
	 *
	 * @param $table_name Name of the main table you're selecting from (= first table in the from clause).
	 * @param $helper Table helper.
	 *
	 * @return GlueDB_Query_Select
	 */
	public static function select($table_name, &$helper = null) {
		return new GlueDB_Query_Select(self::table($table_name), $helper);
	}

	/**
	 * Returns an update query object.
	 *
	 * @param $table_name Name of the main table you're updating (= first table in the update clause).
	 * @param $helper Table helper.
	 *
	 * @return GlueDB_Query_Update
	 */
	public static function update($table_name, &$helper = null) {
		return new GlueDB_Query_Update(self::table($table_name), $helper);
	}

	/**
	 * Returns a delete query object.
	 *
	 * @param $table_name Name of the main table you're deleting from (= first table in the from clause).
	 * @param $helper Table helper.
	 *
	 * @return GlueDB_Query_Delete
	 */
	public static function delete($table_name, &$helper = null) {
		return new GlueDB_Query_Delete(self::table($table_name), $helper);
	}

	/**
	 * Returns a insert query object.
	 *
	 * @param $table_name Name of the table you're inserting data into.
	 * @param $helper Table helper.
	 *
	 * @return GlueDB_Query_Insert
	 */
	public static function insert($table_name, &$helper = null) {
		return new GlueDB_Query_Insert(self::table($table_name), $helper);
	}
}