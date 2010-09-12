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
	 * Quotes values, inserts them into the template, surrounds the whole thing with parenthesis
	 * and inserts it at the begining of the expression.
	 *
	 * @return GlueDB_Fragment_Composite_Boolean
	 */
	public function init($template, $values) {
		$this->reset();
		$this->push(new GlueDB_Fragment_Template($this, $template, $values));
		return $this;
	}

	/**
	 * Use ->or() instead of this. Quotes values, inserts them into the template, surrounds the
	 * whole thing with parenthesis, and inserts it at the end of the expression using the 'OR'
	 * operator.
	 *
	 * @return GlueDB_Fragment_Composite_Boolean
	 */
	public function _or($template, $values) {
		$this->push(new GlueDB_Fragment_Template($this, ' OR (' . $template . ') ', $values));
		return $this;
	}

	/**
	 * Use ->and() instead of this. Quotes values, inserts them into the template, surrounds the
	 * whole thing with parenthesis, and inserts it at the end of the expression using the 'AND'
	 * operator.
	 *
	 * @return GlueDB_Fragment_Composite_Boolean
	 */
	public function _and($template, $values) {
		$this->push(new GlueDB_Fragment_Template($this, ' AND (' . $template . ') ', $values));
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
		// Reset content :
		$this->parts = array();

		// Init builder :
		$builder = new GlueDB_Fragment_Composite_Boolean($this);

		// Add builder :
		$this->add(array($builder));

		return $this;
	}

	/**
	 * Inserts a nested expression into the current expression.
	 *
	 * @param GlueDB_Fragment_Composite_Boolean $builder Initialized with a builder that can be used at a later time
	 * 										  to define the content of the nested expression.
	 *
	 * @return GlueDB_Fragment_Composite_Boolean
	 */
	public function orx(&$builder) {
		// Init builder :
		$builder = new GlueDB_Fragment_Composite_Boolean($this);

		// Add builder :
		$this->add(array($builder), 'OR');

		return $this;
	}

	/**
	 * Inserts a nested expression into the current expression.
	 *
	 * @param GlueDB_Fragment_Composite_Boolean $builder Initialized with a builder that can be used at a later time
	 * 										  to define the content of the nested expression.
	 *
	 * @return GlueDB_Fragment_Composite_Boolean
	 */
	public function andx(&$builder) {
		// Init builder :
		$builder = new GlueDB_Fragment_Composite_Boolean($this);

		// Add builder :
		$this->add(array($builder), 'AND');

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