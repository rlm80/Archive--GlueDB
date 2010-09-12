<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Delete query data structure.
 *
 * @package GlueDB
 * @author Régis Lemaigre
 * @license MIT
 */

class GlueDB_Query_Delete extends GlueDB_Query {
	/*
	 * Executes current query and returns the number of affected rows.
	 *
	 * @see PDO::exec()
	 */
	public function execute() {
		return $this->db->exec($this->compile());
	}
}