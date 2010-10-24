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
	 * @return GlueDB_Table
	 */
	public static function table($table_name) {
		return GlueDB_Table::get($table_name);
	}

	/**
	 * Returns a select query object.
	 *
	 * @param string $table_name Name of the main table you're selecting from (= first table in the from clause).
	 * @param $alias Table alias.
	 *
	 * @return GlueDB_Fragment_Query_Select
	 */
	public static function select($table_name, &$alias = null) {
		$query = new GlueDB_Fragment_Query_Select();
		$query->from($table_name, $alias);
		return $query->from();
	}

	/**
	 * Returns an update query object.
	 *
	 * @param string $table_name Name of the main table you're updating (= first table in the update clause).
	 * @param $alias Table alias.
	 *
	 * @return GlueDB_Fragment_Query_Update
	 */
	public static function update($table_name, &$alias = null) {
		$query = new GlueDB_Fragment_Query_Update();
		$query->from($table_name, $alias);
		return $query->from();
	}

	/**
	 * Returns a delete query object.
	 *
	 * @param string $table_name Name of the table you're deleting from.
	 * @param $alias Table alias.
	 *
	 * @return GlueDB_Fragment_Query_Delete
	 */
	public static function delete($table_name, &$alias = null) {
		$query = new GlueDB_Fragment_Query_Delete();
		$query->from($table_name, $alias);
		return $query;
	}

	/**
	 * Returns a insert query object.
	 *
	 * @param string $table_name Name of the table you're inserting data into.
	 * @param $alias Table alias.
	 *
	 * @return GlueDB_Fragment_Query_Insert
	 */
	public static function insert($table_name, &$alias = null) {
		$query = new GlueDB_Fragment_Query_Insert();
		$query->from($table_name, $alias);
		return $query->from();
	}

	/**
	 * Returns a new value fragment.
	 *
	 * @param mixed $value
	 *
	 * @return GlueDB_Fragment_Value
	 */
	public static function value($value = null) {
		return new GlueDB_Fragment_Value($value);
	}

	/**
	 * Returns a new table fragment.
	 *
	 * @param string $table_name
	 * @param string $alias Null means you let the system choose a unique alias. If you don't want an alias at all, pass an empty string.
	 *
	 * @return GlueDB_Fragment_Table
	 */
	public static function alias($table_name, $alias = null) {
		return new GlueDB_Fragment_Aliased_Table($table_name, $alias);
	}

	/**
	 * Returns a new template fragment.
	 *
	 * @return GlueDB_Fragment_Template
	 */
	public static function template() {
		$values		= func_get_args();
		$template	= array_shift($values);
		return new GlueDB_Fragment_Template($template, $values);
	}

	/**
	 * Returns a new boolean fragment.
	 *
	 * @return GlueDB_Fragment_Builder_Bool
	 */
	public static function bool() {
		$f = new GlueDB_Fragment_Builder_Bool();
		if (func_num_args() > 0) {
			$args = func_get_args();
			call_user_func_array(array($f, 'init'), $args);
		}
		return $f;
	}

	/**
	 * Returns a new join fragment.
	 *
	 * @param string $table_name
	 * @param string $alias
	 *
	 * @return GlueDB_Fragment_Builder_Join
	 */
	public static function join($table_name, &$alias = null) {
		$f = new GlueDB_Fragment_Builder_Join();
		$f->init($table_name, $alias);
		return $f;
	}
}