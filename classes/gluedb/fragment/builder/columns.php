<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build a list of columns.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Builder_Columns extends GlueDB_Fragment_Builder {
	/**
	 * Adds an column at the end of the columns list.
	 *
	 * @param GlueDB_Fragment_Column $column
	 *
	 * @return GlueDB_Fragment_Builder_Columns
	 */
	public function _and(GlueDB_Fragment_Column $column) {
		$this->push($column);
		return $this;
	}
	
	/**
	 * Forwards call to given database.
	 *
	 * @param GlueDB_Database $db
	 * @param integer $style
	 *
	 * @return string
	 */
	protected function compile(GlueDB_Database $db, $style) {
		// Forwards call to database :
		return $db->compile_builder_columns($this, $style);
	}	
}