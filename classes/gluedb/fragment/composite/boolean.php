<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build a boolean expression.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Composite_Boolean extends GlueDB_Fragment_Composite {
	/**
	 * Initializes the expression with a first boolean operand.
	 *
	 * @return GlueDB_Fragment_Composite_Boolean
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
	 * Use ->or() instead of this. Adds a boolean operand at the end of the expression, connecting it with
	 * the OR operator.
	 *
	 * @return GlueDB_Fragment_Composite_Boolean
	 */
	public function _or() {
		// Get template, replacement values and create SQL string :
		$values		= func_get_args();
		$template	= array_shift($values);

		// Add boolean operand :
		$this->push(new GlueDB_Fragment_Template(' OR ' . $template, $values));

		return $this;
	}

	/**
	 * Use ->and() instead of this. Adds a boolean operand at the end of the expression, connecting it with
	 * the AND operator.
	 *
	 * @return GlueDB_Fragment_Composite_Boolean
	 */
	public function _and() {
		// Get template, replacement values and create SQL string :
		$values		= func_get_args();
		$template	= array_shift($values);

		// Add boolean operand :
		$this->push(new GlueDB_Fragment_Template(' AND ' . $template, $values));

		return $this;
	}

	/*
	 * Redefined to setup aliases for _or() and _and(). Required because
	 * keywords aren't valid function names in PHP.
	 */
	public function __call($name, $args) {
		if ($name === 'or')
			return call_user_func_array(array($this, '_or'), $args);
		elseif ($name === 'and')
			return call_user_func_array(array($this, '_and'), $args);
	}
}