<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Base formatter class.
 *
 * A formatter is an object that converts raw data coming from the database (usually
 * only strings) into a format suitable for PHP, and the other way around.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Formatter {
	/**
	 * Formats data coming from the database into a format suitable for PHP.
	 *
	 * @param mixed $data
	 */
	abstract public function format($data);

	/**
	 * Formats data coming from PHP into a format suitable for insertino into the database.
	 *
	 * @param mixed $data
	 */
	abstract public function unformat($data);

	/**
	 * The PHP type returned by the format function.
	 *
	 * @return string
	 */
	abstract public function type();
}
