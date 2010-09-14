<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a boolean expression.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Composite_Boolean extends GlueDB_Fragment_Composite {
	/**
	 * Initializes the expression with a first boolean operand.
	 *
	 * Quotes values, inserts them into the template, surrounds the whole thing with parentheses
	 * and inserts the result at the end of the expression. Calling pop() once will remove it all.
	 *
	 * @return GlueDB_Fragment_Composite_Boolean
	 */
	public function init() {
		// Get template and replacement values :
		$values		= func_get_args();
		$template	= array_shift($values);

		// Remove children :
		$this->reset();

		// Add boolean operand :
		$this->push(array(
			new GlueDB_Fragment_Template(' ( '),
			new GlueDB_Fragment_Template($template, $values),
			new GlueDB_Fragment_Template(' ) '),
		));

		return $this;
	}

	/**
	 * Use ->or() instead of this. Adds a boolean operand at the end of the expression, connecting it with
	 * the OR operator.
	 *
	 * Quotes values, inserts them into the template, surrounds the whole thing with parentheses
	 * and inserts the result at the end of the expression. Calling pop() once will remove it all.
	 *
	 * @return GlueDB_Fragment_Composite_Boolean
	 */
	public function _or() {
		// Get template and replacement values :
		$values		= func_get_args();
		$template	= array_shift($values);

		// Add boolean operand :
		$this->push(array(
			new GlueDB_Fragment_Template(' OR ( '),
			new GlueDB_Fragment_Template($template, $values),
			new GlueDB_Fragment_Template(' ) '),
		));

		return $this;
	}

	/**
	 * Use ->and() instead of this. Adds a boolean operand at the end of the expression, connecting it with
	 * the AND operator.
	 *
	 * Quotes values, inserts them into the template, surrounds the whole thing with parentheses
	 * and inserts the result at the end of the expression. Calling pop() once will remove it all.
	 *
	 * @return GlueDB_Fragment_Composite_Boolean
	 */
	public function _and() {
		// Get template and replacement values :
		$values		= func_get_args();
		$template	= array_shift($values);

		// Add boolean operand :
		$this->push(array(
			new GlueDB_Fragment_Template(' AND ( '),
			new GlueDB_Fragment_Template($template, $values),
			new GlueDB_Fragment_Template(' ) '),
		));

		return $this;
	}

	/**
	 * Initializes the expression with a nested expression.
	 *
	 * @param GlueDB_Fragment_Composite_Boolean $builder Initialized with a builder that can be used at a later time
	 * 										  to define the content of the nested expression.
	 *
	 * @return GlueDB_Fragment_Composite_Boolean
	 */
	public function initx(&$builder) {
		// Remove children :
		$this->reset();

		// Add boolean operand :
		$this->push(array(
			new GlueDB_Fragment_Template(' ( '),
			$builder = new GlueDB_Fragment_Composite_Boolean(),
			new GlueDB_Fragment_Template(' ) '),
		));

		return $this;
	}

	/**
	 * Adds a nested expression at the end of the expression, connecting it with
	 * the OR operator.
	 *
	 * @param GlueDB_Fragment_Composite_Boolean $builder Initialized with a builder that can be used at a later time
	 * 										  to define the content of the nested expression.
	 *
	 * @return GlueDB_Fragment_Composite_Boolean
	 */
	public function orx(&$builder) {
		// Add boolean operand :
		$this->push(array(
			new GlueDB_Fragment_Template(' OR ( '),
			$builder = new GlueDB_Fragment_Composite_Boolean(),
			new GlueDB_Fragment_Template(' ) '),
		));

		return $this;
	}

	/**
	 * Adds a nested expression at the end of the expression, connecting it with
	 * the AND operator.
	 *
	 * @param GlueDB_Fragment_Composite_Boolean $builder Initialized with a builder that can be used at a later time
	 * 										  to define the content of the nested expression.
	 *
	 * @return GlueDB_Fragment_Composite_Boolean
	 */
	public function andx(&$builder) {
		// Add boolean operand :
		$this->push(array(
			new GlueDB_Fragment_Template(' AND ( '),
			$builder = new GlueDB_Fragment_Composite_Boolean(),
			new GlueDB_Fragment_Template(' ) '),
		));

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