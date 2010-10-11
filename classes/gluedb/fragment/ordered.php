<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents anything that compiles into "... ASC" or "... DESC".
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Fragment_Ordered extends GlueDB_Fragment {
	/**
	 * @var boolean Whether or not order is ascending.
	 */
	protected $asc;

	/**
	 * Order setter.
	 *
	 * @param boolean $asc
	 */
	public function set_asc($asc = true) {
		$this->asc = $asc;
		$this->invalidate();
	}

	/**
	 * Returns order.
	 *
	 * @return string
	 */
	public function asc() {
		return $this->asc;
	}


	/**
	 * Compiles the data structure against given database and returns the
	 * resulting SQL string.
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	protected function compile($dbname) {
		$db		= gluedb::db($dbname);
		$sqldef	= $this->compile_definition($dbname);
		return $db->compile_ordered($sqldef, $this->asc());
	}

	/**
	 * Returns SQL string for everything that must come before the " ASC"/" DESC".
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	abstract protected function compile_definition($dbname);
}