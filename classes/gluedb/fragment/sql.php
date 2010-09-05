<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that holds a piece of SQL that requires no conversion.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_SQL extends GlueDB_Fragment {
	/**
	 * @var string Internal SQL string
	 */
	protected $sqlstr;
	
	/**
	 * Constructor.
	 *
	 * @param string $sqlstr Initial value of internal SQL string.
	 * @param GlueDB_Fragment $parent
	 */
	public function __construct($sqlstr, GlueDB_Fragment $parent = null) {
		$this->parent = $parent;
		$this->sqlstr = $sqlstr;
	}	
	
	/**
	 * Sets internal SQL string and invalidates fragment.
	 * 
	 * @param string $sqlstr
	 */
	public function set_sql($sqlstr) {
		$this->sqlstr = $sqlstr;
		$this->invalidate();
	}
	
	/**
	 * Compiles the data structure against given database and returns the
	 * resulting SQL string. In this case, simply returns the internal SQL
	 * string as is.
	 * 
	 * @param string $dbname
	 * 
	 * @return string
	 */
	protected function compile($dbname) {
		return $str;
	}
}