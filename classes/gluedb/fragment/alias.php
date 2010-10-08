<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents anything that compiles into "... AS <alias>".
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Fragment_Alias extends GlueDB_Fragment {
	/**
	 * @var string.
	 */
	protected $alias;

	/**
	 * Constructor.
	 *
	 * @param string $alias
	 */
	public function __construct($alias) {
		$this->alias = $alias;
	}

	/**
	 * Alias setter.
	 *
	 * @param string $alias
	 */
	public function set_alias($alias) {
		$this->alias = $alias;
		$this->invalidate();
	}

	/**
	 * Returns alias.
	 *
	 * @return string
	 */
	public function alias() {
		return $this->alias;
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
		if ($this->alias() === '')
			return $sqldef;
		else
			return $db->compile_alias($sqldef, $this->alias());
	}

	/**
	 * Returns SQL string for everything that must come before the "AS".
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	abstract protected function compile_definition($dbname);
}