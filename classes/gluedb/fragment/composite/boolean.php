<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a boolean expression.
 *
 * TODO DRYify the whole thing....
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Composite_Boolean extends GlueDB_Fragment_Composite {
	/**
	 * Quotes values, inserts them into the template, surrounds the whole thing with parenthesis
	 * and inserts the result at the begining of the expression.
	 *
	 * @return GlueDB_Fragment_Composite_Boolean
	 */
	public function init($template, $values) {
		// Remove children :
		$this->reset();

		// Add new fragment (necessary to make this atomic so that pop() removes the whole thing in one go) :
		$fragment = new GlueDB_Fragment_Composite($this);
		$this->push($fragment);

		// Build fragment :
		$fragment->push(' ( ');
		$fragment->push(new GlueDB_Fragment_Template($fragment, $template, $values));
		$fragment->push(' ) ');

		return $this;
	}

	/**
	 * Use ->or() instead of this. Quotes values, inserts them into the template, surrounds the
	 * whole thing with parenthesis, and inserts the result at the end of the expression using the 'OR'
	 * operator.
	 *
	 * @return GlueDB_Fragment_Composite_Boolean
	 */
	public function _or($template, $values) {
		// Add new fragment (necessary to make this atomic so that pop() removes the whole thing in one go) :
		$fragment = new GlueDB_Fragment_Composite($this);
		$this->push($fragment);

		// Build fragment :
		$fragment->push(' OR (');
		$fragment->push(new GlueDB_Fragment_Template($fragment, $template, $values));
		$fragment->push(' ) ');

		return $this;
	}

	/**
	 * Use ->and() instead of this. Quotes values, inserts them into the template, surrounds the
	 * whole thing with parenthesis, and inserts the result at the end of the expression using the 'AND'
	 * operator.
	 *
	 * @return GlueDB_Fragment_Composite_Boolean
	 */
	public function _and($template, $values) {
		// Add new fragment (necessary to make this atomic so that pop() removes the whole thing in one go) :
		$fragment = new GlueDB_Fragment_Composite($this);
		$this->push($fragment);

		// Build fragment :
		$fragment->push(' AND (');
		$fragment->push(new GlueDB_Fragment_Template($fragment, $template, $values));
		$fragment->push(' ) ');

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

		// Add new fragment (necessary to make this atomic so that pop() removes the whole thing in one go) :
		$fragment = new GlueDB_Fragment_Composite($this);
		$this->push($fragment);

		// Build fragment :
		$fragment->push(' OR (');
		$fragment->push($builder = new GlueDB_Fragment_Composite_Boolean($this));
		$fragment->push(' ) ');

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
		// Add new fragment (necessary to make this atomic so that pop() removes the whole thing in one go) :
		$fragment = new GlueDB_Fragment_Composite($this);
		$this->push($fragment);

		// Build fragment :
		$fragment->push(' OR (');
		$fragment->push($builder = new GlueDB_Fragment_Composite_Boolean($this));
		$fragment->push(' ) ');

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
		// Add new fragment (necessary to make this atomic so that pop() removes the whole thing in one go) :
		$fragment = new GlueDB_Fragment_Composite($this);
		$this->push($fragment);

		// Build fragment :
		$fragment->push(' AND (');
		$fragment->push($builder = new GlueDB_Fragment_Composite_Boolean($this));
		$fragment->push(' ) ');

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