<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a join expression.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Composite_Join extends GlueDB_Fragment_Composite {
	/**
	 * @var GlueDB_Fragment_Composite_Boolean Boolean builder that is the current target for on, or, and, onx,
	 * 										  orx, andx calls.
	 */
	protected $boolean_target;

	/**
	 * Initializes the expression with the given table.
	 *
	 * @param string $table Table name.
	 * @param GlueDB_Helper_Table $helper Initialized with a table helper that may be used at a later time to
	 *									  refer to the table columns.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function init($table, &$helper) {
		// Create table helper :
		$helper = gluedb::table($table)->helper($this->root());

		// Add helper :
		$this->parts[] = $helper;
		$this->parts[] = ' ';

		// Invalidate :
		$this->invalidate();

		return $this;
	}

	/**
	 * Adds an inner join to current expression.
	 *
	 * @param string $table Virtual table name.
	 * @param GlueDB_Helper_Table $helper Initialized with a table helper that may be used at a later time to
	 *									  refer to the table columns.
	 * @param GlueDB_Builder_Boolean $builder Initialized with a boolean builder that may be used at a later time
	 * 										  to keep working on the ON clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function inner($table, &$helper, &$builder = null) {
		$this->join($table, $helper, $builder, 'INNER JOIN');
		return $this;
	}

	/**
	 * Adds an left outer join to current expression.
	 *
	 * @param string $table Virtual table name.
	 * @param GlueDB_Helper_Table $helper Initialized with a table helper that may be used at a later time to
	 *									  refer to the table columns.
	 * @param GlueDB_Builder_Boolean $builder Initialized with a boolean builder that may be used at a later time
	 * 										  to keep working on the ON clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function left($table, &$helper, &$builder = null) {
		$this->join($table, $helper, $builder, 'LEFT OUTER JOIN');
		return $this;
	}

	/**
	 * Adds an right outer join to current expression.
	 *
	 * @param string $table Virtual table name.
	 * @param GlueDB_Helper_Table $helper Initialized with a table helper that may be used at a later time to
	 *									  refer to the table columns.
	 * @param GlueDB_Builder_Boolean $builder Initialized with a boolean builder that may be used at a later time
	 * 										  to keep working on the ON clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function right($table, &$helper, &$builder = null) {
		$this->join($table, $helper, $builder, 'RIGHT OUTER JOIN');
		return $this;
	}

	/**
	 * Adds a join to current expression with given connector.
	 *
	 * @param string $table Virtual table name.
	 * @param GlueDB_Helper_Table $helper Initialized with a table helper that may be used at a later time to
	 *									  refer to the table columns.
	 * @param GlueDB_Builder_Boolean $builder Initialized with a boolean builder that may be used at a later time
	 * 										  to keep working on the ON clause.
	 * @param string $connector
	 *
	 * @return GlueDB_Builder_Join
	 */
	protected function join($table, &$helper, &$builder, $connector) {
		// Create table helper :
		$helper = gluedb::table($table)->helper(); // TODO passer query au constructeur ?

		// Create boolean builder :
		$builder = new GlueDB_Builder_Boolean($this);

		// Add join :
		$this->parts[] = ' ' . $connector . ' ';
		$this->parts[] = $helper;
		$this->parts[] = ' ON (';
		$this->parts[] = $builder;
		$this->parts[] = ') ';

		// Set new boolean target :
		$this->boolean_target = $builder;

		// Invalidate :
		$this->invalidate();
	}

	/**
	 * Adds an inner join expression to current expression with given connector. The first parameter is filled
	 * with a join expression builder and the second with a boolean builer that you may use at a later time
	 * to build the join expression and its on clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function innerx(&$joinbuilder, &$onbuilder) {
		$this->joinx($joinbuilder, $onbuilder, 'INNER JOIN');
		return $this;
	}

	/**
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function leftx(&$joinbuilder, &$onbuilder) {
		$this->joinx($joinbuilder, $onbuilder, 'LEFT OUTER JOIN');
		return $this;
	}

	/**
	 * Adds a left outer join expression to current expression with given connector. The first parameter is filled
	 * with a join expression builder and the second with a boolean builer that you may use at a later time
	 * to build the join expression and its on clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function rightx(&$joinbuilder, &$onbuilder) {
		$this->joinx($joinbuilder, $onbuilder, 'RIGHT OUTER JOIN');
		return $this;
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
	}

	/**
	 * Forwards call to boolean builder of the last on clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function on() {
		$args = func_get_args();
		call_user_func_array(array($this->boolean_target, 'init'), $args);
		return $this;
	}

	/**
	 * Forwards call to boolean builder of the last on clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function onx(&$builder) {
		$this->boolean_target->initx($builder);
		return $this;
	}

	/**
	 * Forwards call to boolean builder of the last on clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function _or() {
		$args = func_get_args();
		call_user_func_array(array($this->boolean_target, '_or'), $args);
		return $this;
	}

	/**
	 * Forwards call to boolean builder of the last on clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function _and() {
		$args = func_get_args();
		call_user_func_array(array($this->boolean_target, '_and'), $args);
		return $this;
	}

	/**
	 * Forwards call to boolean builder of the last on clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function orx(&$builder) {
		$this->boolean_target->orx($builder);
		return $this;
	}

	/**
	 * Forwards call to boolean builder of the last on clause.
	 *
	 * @return GlueDB_Builder_Join
	 */
	public function andx(&$builder) {
		$this->boolean_target->andx($builder);
		return $this;
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