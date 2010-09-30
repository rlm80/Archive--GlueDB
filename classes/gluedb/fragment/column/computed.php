<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a computed column definition in a select list.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Column_Computed extends GlueDB_Fragment_Column {
	/**
	 * @var GlueDB_Fragment_Template
	 */
	protected $template;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment_Template $fragment
	 */
	public function __construct(GlueDB_Fragment_Template $fragment) {
		$this->template = $fragment;
		$this->template->register_user($this);
	}

	/**
	 * Returns column alias.
	 */
	protected function alias() {
		if ( ! isset($this->alias))
			throw new Kohana_Exception("Computed columns must have an alias.");
		return $this->alias;
	}

	/**
	 * Returns column definition SQL.
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	protected function compile_definition($dbname) {
		return $this->template->sql($dbname);
	}
}