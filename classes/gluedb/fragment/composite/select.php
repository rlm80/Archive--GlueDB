<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that provides a fluent interface to build a select clause.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Composite_Select extends GlueDB_Fragment_Composite {
	/**
	 * @var Query that owns this order by clause.
	 */
	protected $query;

	/**
	 * @param GlueDB_Query $query
	 */
	public function __construct(GlueDB_Query $query) {
		$this->query = $query;
	}

	/**
	 * Forwards unknown calls to query.
	 *
	 * @param unknown_type $name
	 * @param unknown_type $args
	 *
	 * @return mixed
	 */
	public function __call($name, $args) {
		return call_user_func_array(array($this->query, $name), $args);
	}

	/**
	 * Initializes the list with a first element.
	 *
	 * @return GlueDB_Fragment_Composite_Select
	 */
	public function init() {
		// Get template, replacement values and create SQL string :
		$values		= func_get_args();
		$template	= array_shift($values);

		// Remove children :
		$this->reset();

		// Add fragment :
		$this->push(new GlueDB_Fragment_Template_Alias($template, $values));

		return $this;
	}

	/**
	 * Adds an element operand at the end of the list.
	 *
	 * @return GlueDB_Fragment_Composite_Select
	 */
	public function then() {
		// Get template, replacement values and create SQL string :
		$values		= func_get_args();
		$template	= array_shift($values);

		// Add fragment :
		$this->push(new GlueDB_Fragment_Template_Alias($template, $values));

		return $this;
	}

	/**
	 * Sets alias of the last element of the list.
	 *
	 * @return GlueDB_Fragment_Composite_Select
	 */
	public function _as($alias) {
		if ($last = $this->last())
			$last->as($alias);
		else
			throw new Kohana_Exception("No column to set an alias to.");

		return $this;
	}

	/*
	 * Redefined to setup aliases for _as(). Required because
	 * keywords aren't valid function names in PHP.
	 */
	public function __call($name, $args) {
		if ($name === 'as')
			return call_user_func_array(array($this, '_as'), $args);
	}
}