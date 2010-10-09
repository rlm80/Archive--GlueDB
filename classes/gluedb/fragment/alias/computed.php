<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a computed column - alias pair in a select list.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Alias_Computed extends GlueDB_Fragment_Alias {
	/**
	 * @var GlueDB_Fragment_Template Template.
	 */
	protected $template;

	/**
	 * Constructor.
	 *
	 * @param string $template
	 * @param array $values
	 * @param string $alias
	 */
	public function __construct($template, $values, $alias) {
		parent::__construct($alias);
		$this->template	= new GlueDB_Fragment_Template($template, $values);
		$this->template->register_user($this);
	}

	/**
	 * Returns SQL string for everything that must come before the "AS".
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	protected function compile_definition($dbname) {
		return $this->template->sql($dbname);
	}
}