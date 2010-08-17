<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Virtual table that map to several other virutal tables joined together by pk.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Table_Composite extends GlueDB_Table_Base {
	/**
	 * Returns the columns of this virtual table.
	 */
	protected function create_columns() {

	}
}
