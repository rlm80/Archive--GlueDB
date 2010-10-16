<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a computed column - alias pair in a select list.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Aliased_Computed extends GlueDB_Fragment_Aliased {
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
	 * Generates unique alias.
	 *
	 * @return string
	 */
	protected function create_alias() {
		throw new Kohana_Exception("Should never happen !!! We always set a default alias for this.");
	}

	/**
	 * Returns SQL string for everything that must come before the "AS".
	 *
	 * @param GlueDB_Database $db
	 *
	 * @return string
	 */
	protected function compile_definition(GlueDB_Database $db) {
		return $this->template->sql($db->name());
	}
}