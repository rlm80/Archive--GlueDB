<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build a list of columns or SQL expressions.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Composite_List extends GlueDB_Fragment_Composite {
	/**
	 * Initializes the list with a first element.
	 *
	 * @return GlueDB_Fragment_Composite_List
	 */
	public function init() {
		// Get template, replacement values and create SQL string :
		$values		= func_get_args();
		$template	= array_shift($values);

		// Remove children :
		$this->reset();

		// Add boolean operand :
		$this->push(new GlueDB_Fragment_Template($template, $values));

		return $this;
	}

	/**
	 * Adds an element operand at the end of the list.
	 *
	 * @return GlueDB_Fragment_Composite_List
	 */
	public function then() {
		// Get template, replacement values and create SQL string :
		$values		= func_get_args();
		$template	= array_shift($values);

		// Add boolean operand :
		$this->push(new GlueDB_Fragment_Template(' , ' . $template, $values));

		return $this;
	}
}