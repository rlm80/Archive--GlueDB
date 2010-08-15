<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * String formatter class.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Formatter_String extends GlueDB_Formatter {
	
	/**
	 * Formats data coming from the database into a format suitable for PHP.
	 * 
	 * @param integer $data
	 */
	public function format($data) {
		if (isset($data))
			return (string) $data;
		else
			return null;		
	}
	
	/**
	 * Formats data coming from PHP into a format suitable for insertino into the database.
	 * 
	 * @param integer $data
	 */
	public function unformat($data) {
		return $data;
	}
}
