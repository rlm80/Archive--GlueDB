<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
* Main GlueDB class. Contains only static methods. All of the GlueDB features can be
* accessed through this interface. Whatever you do with GlueDB, this should almost
* always be your entry point.
*
* @package Glue
* @author Régis Lemaigre
* @license MIT
*/

class GlueDB {
	public static function database($name = 'default') {
		return GludDB_Database::get($name);
	}
}