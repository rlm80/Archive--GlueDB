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
	 * instance, instead of creating a new one.
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
	 * virtual table instance, instead of creating a new one.
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
	 * @param GlueDB_Fragment_Aliased_Table $alias Table alias object you may use to refer to the table columns.
	 *
	 * @return GlueDB_Fragment_Query_Select
	 */
	public static function select($table_name = null, &$alias = null) {
		$f = new GlueDB_Fragment_Query_Select();
		if (func_num_args() > 0) {
			$args = func_get_args();
			return $f->from($table_name, $alias);
		}
		else
			return $f;
	}

	/**
	 * Returns an update query object.
	 *
	 * @param string $table_name Name of the main table you're updating (= first table in the update clause).
	 * @param GlueDB_Fragment_Aliased_Table $alias Table alias object you may use to refer to the table columns.
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
	 * @param GlueDB_Fragment_Aliased_Table $alias Table alias object you may use to refer to the table columns.
	 *
	 * @return GlueDB_Fragment_Query_Delete
	 */
	public static function delete($table_name, &$alias = null) {
		return new GlueDB_Fragment_Query_Delete($table_name, $alias);
	}

	/**
	 * Returns a insert query object.
	 *
	 * @param string $table_name Name of the table you're inserting data into.
	 * @param GlueDB_Fragment_Aliased_Table $alias Table alias object you may use to refer to the table columns.
	 *
	 * @return GlueDB_Fragment_Query_Insert
	 */
	public static function insert($table_name, &$alias = null) {
		return new GlueDB_Fragment_Query_Insert($table_name, $alias);
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
	 * @param string $alias
	 *
	 * @return GlueDB_Fragment_Table
	 */
	public static function alias($table_name, $alias = null) {  // TODO : would it be possible to rename this "table" and define
																// a toString method in the table fragments ? so that the aliasing
																// system may still be used with custom queries
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
	 * @return mixed
	 */
	public static function join($table_name = null, &$alias = null) {
		$f = new GlueDB_Fragment_Builder_Join();
		if (func_num_args() > 0) {
			$args = func_get_args();
			return $f->init($table_name, $alias);
		}
		else
			return $f;
	}
}