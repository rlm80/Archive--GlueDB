<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a computed column in an order by clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Ordered_Computed extends GlueDB_Fragment_Ordered {
	/**
	 * @var GlueDB_Fragment_Template Template.
	 */
	protected $template;

	/**
	 * Constructor.
	 *
	 * @param string $template
	 * @param array $values
	 */
	public function __construct($template, $values) {
		$this->template	= new GlueDB_Fragment_Template($template, $values);
		$this->template->register_user($this);
	}

	/**
	 * Returns SQL string for everything that must come before the " ASC"/" DESC".
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	protected function compile_definition($dbname) {
		return $this->template->sql($dbname);
	}
}