<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Join expression builder class.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Builder_Join extends GlueDB_Builder {
	/**
	 * @var GlueDB_Builder_Boolean Boolean builder that is the current target for or, and, orx, andx calls.
	 */
	protected $boolean_target;

	/**
	 * Initializes the expression with the given table.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function init($table, &$helper) {
		// Create table helper :
		$helper = gluedb::table($table)->helper(); // TODO passer this ! parce que on builder en a besoin (context)

		// Add helper :
		$this->parts[] = $helper;
		$this->parts[] = ' ';

		// Set new boolean target :
		$this->boolean_target = $helper->on();

		// Invalidate :
		$this->invalidate();

		return $this;
	}

	/**
	 * Adds an inner join table to current expression. The second parameter is filled with a table
	 * helper that may be used at a later time to keep building on the ON clause of the table instance
	 * in the query or to refer to its columns.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function inner($table, &$helper) {
		return $this->join($table, $helper, 'INNER JOIN');
	}

	/**
	 * Adds an left outer join table to current expression. The second parameter is filled with a table
	 * helper that may be used at a later time to keep building on the ON clause of the table instance
	 * in the query or to refer to its columns.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function left($table, &$helper) {
		return $this->join($table, $helper, 'LEFT OUTER JOIN');
	}

	/**
	 * Adds an right outer join table to current expression. The second parameter is filled with a table
	 * helper that may be used at a later time to keep building on the ON clause of the table instance
	 * in the query or to refer to its columns.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function right($table, &$helper) {
		return $this->join($table, $helper, 'RIGHT OUTER JOIN');
	}

	/**
	 * Adds a join table to current expression with given connector. The second parameter is filled with a table
	 * helper that may be used at a later time to keep building on the ON clause of the table instance
	 * in the query or to refer to its columns.
	 *
	 * @return GlueDB_Builder_Join
	 */
	protected function join($table, &$helper, $connector) {
		// Create table helper :
		$helper = gluedb::table($table)->helper(); // TODO passer this ! parce que on builder en a besoin (context)

		// Add join :
		$this->parts[] = ' ' . $connector . ' ';
		$this->parts[] = $helper;
		$this->parts[] = ' ON (';
		$this->parts[] = $helper->on();
		$this->parts[] = ') ';

		// Set new boolean target :
		$this->boolean_target = $helper->on();

		// Invalidate :
		$this->invalidate();

		return $this;
	}

	/**
	 * Adds an inner join expression to current expression with given connector. The first parameter is filled
	 * with a join expression builder and the second with a boolean builer that you may use at a later time
	 * to build the join expression and its on clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function innerx(&$joinbuilder, &$onbuilder) {
		return $this->joinx($joinbuilder, $onbuilder, 'INNER JOIN');
	}

	/**
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function leftx(&$joinbuilder, &$onbuilder) {
		return $this->joinx($joinbuilder, $onbuilder, 'LEFT OUTER JOIN');
	}

	/**
	 * Adds a left outer join expression to current expression with given connector. The first parameter is filled
	 * with a join expression builder and the second with a boolean builer that you may use at a later time
	 * to build the join expression and its on clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function rightx(&$joinbuilder, &$onbuilder) {
		return $this->joinx($joinbuilder, $onbuilder, 'RIGHT OUTER JOIN');
	}

	/**
	 * Adds a right outer join expression to current expression with given connector. The first parameter is filled
	 * with a join expression builder and the second with a boolean builer that you may use at a later time
	 * to build the join expression and its on clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	protected function joinx(&$joinbuilder, &$onbuilder, $connector) {
		// Create new builders :
		$joinbuilder	= new GlueDB_Builder_Join($this);
		$onbuilder		= new GlueDB_Builder_Boolean($this);

		// Add join :
		$this->parts[] = ' ' . $connector . ' (';
		$this->parts[] = $joinbuilder;
		$this->parts[] = ') ON (';
		$this->parts[] = $onbuilder;
		$this->parts[] = ') ';

		// Set new boolean target :
		$this->boolean_target = $onbuilder;

		// Invalidate :
		$this->invalidate();

		return $this;
	}

	/**
	 * Forwards call to boolean builder of the last on clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function on() {
		$args = func_get_args();
		call_user_func_array(arra($this->boolean_target, 'init'), $args);
		return this;
	}

	/**
	 * Forwards call to boolean builder of the last on clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function onx(&$builder) {
		$this->boolean_target->initx($builder);
		return this;
	}

	/**
	 * Forwards call to boolean builder of the last on clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function _or() {
		$args = func_get_args();
		call_user_func_array(arra($this->boolean_target, '_or'), $args);
		return this;
	}

	/**
	 * Forwards call to boolean builder of the last on clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function _and() {
		$args = func_get_args();
		call_user_func_array(arra($this->boolean_target, '_and'), $args);
		return this;
	}

	/**
	 * Forwards call to boolean builder of the last on clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function orx(&$builder) {
		$this->boolean_target->orx($builder);
		return this;
	}

	/**
	 * Forwards call to boolean builder of the last on clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function andx(&$builder) {
		$this->boolean_target->andx($builder);
		return this;
	}

	/*
	 * Setup aliases for _or() and _and(). Required because keywords aren't valid function names in PHP.
	 */
	public function __call($name, $args) {
		if ($name === 'or')
			return call_user_func_array(array($this, '_or'), $args);
		elseif ($name === 'and')
			return call_user_func_array(array($this, '_and'), $args);
	}
}