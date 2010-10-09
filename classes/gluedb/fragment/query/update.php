<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Update query data structure.
 *
 * @package GlueDB
 * @author R�gis Lemaigre
 * @license MIT
 */

class GlueDB_Fragment_Query_Update extends GlueDB_Fragment_Query {
	/*
	 * Executes current query and returns the number of affected rows.
	 *
	 * @see PDO::exec()
	 */
	public function execute() {
		return $this->db->exec($this->compile());
	}
}