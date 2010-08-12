<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Select query data structure.
 *
 * @package GlueDB
 * @author Régis Lemaigre
 * @license MIT
 */

class GlueDB_Query_Select extends GlueDB_Query {
	/*
	 * Executes current query and returns a result set.
	 *
	 * @see PDO::query()
	 */
	public function execute($arg1 = null, $arg2 = null, $arg3 = null) {
		return $this->db->query($this->compile(), $arg1, $arg2, $arg3);
	}
}