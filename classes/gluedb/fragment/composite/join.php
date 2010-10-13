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
	 * @var string Connector redefined.
	 */
	protected $connector = ' ';

	/**
	 * Initializes the expression with a first operand.
	 *
	 * @param mixed $operand Table name, aliased table fragment or join fragment.
	 * @param GlueDB_Fragment_Aliased_Table $alias Initialiazed with an aliased table fragment that may be used later on to refer to columns.
	 *
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function init($operand, &$alias = null) {
		$this->reset();
		$this->join($operand, null, $alias);
		return $this;
	}

	/**
	 * Adds an operand to the expression, using an inner join.
	 *
	 * @param mixed $operand Table name, aliased table fragment or join fragment.
	 * @param GlueDB_Fragment_Aliased_Table $alias Initialiazed with an aliased table fragment that may be used later on to refer to columns.
	 *
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function inner($operand, &$alias = null) {
		$this->join($operand, GlueDB_Fragment_Joinop::INNER_JOIN, $alias);
		return $this;
	}

	/**
	 * Adds an operand to the expression, using an left outer join.
	 *
	 * @param mixed $operand Table name, aliased table fragment or join fragment.
	 * @param GlueDB_Fragment_Aliased_Table $alias Initialiazed with an aliased table fragment that may be used later on to refer to columns.
	 *
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function left($operand, &$alias = null) {
		$this->join($operand, GlueDB_Fragment_Joinop::LEFT_OUTER_JOIN, $alias);
		return $this;
	}

	/**
	 * Adds an operand to the expression, using an right outer join.
	 *
	 * @param mixed $operand Table name, aliased table fragment or join fragment.
	 * @param GlueDB_Fragment_Aliased_Table $alias Initialiazed with an aliased table fragment that may be used later on to refer to columns.
	 *
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function right($operand, &$alias = null) {
		$this->join($operand, GlueDB_Fragment_Joinop::RIGHT_OUTER_JOIN, $alias);
		return $this;
	}

	/**
	 * Adds an operand to the expression.
	 *
	 * @param mixed $operand Table name, aliased table fragment or join fragment.
	 * @param integer $operator Operator.
	 * @param GlueDB_Fragment_Aliased_Table $alias Initialiazed with an aliased table fragment that may be used later on to refer to columns.
	 */
	protected function join($operand, $operator, &$alias) {
		// Operand is a table name ? Turn it into an aliased table fragment and set alias :
		if (is_string($operand)) {
			$operand	= new GlueDB_Fragment_Aliased_Table($operand);
			$alias		= $operand;
		}

		// Add operand :
		if ($operand instanceof GlueDB_Fragment_Aliased_Table)
			$this->push(new GlueDB_Fragment_Joinop_Simple($operand, $operator));
		elseif ($operand instanceof GlueDB_Fragment_Composite_Join)
			$this->push(new GlueDB_Fragment_Joinop_Nested($operand, $operator));
		else
			throw new Kohana_Exception("Given join operand is neither GlueDB_Fragment_Aliased_Table nor GlueDB_Fragment_Composite_Join.");
	}

	/**
	 * Forwards call to last operand.
	 *
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function _as($alias) {
		$last = $this->last();
		if (isset($last) && $last instanceof GlueDB_Fragment_Joinop_Simple)
			$last->operand()->set_alias($alias);
		else
			throw new Kohana_Exception("Cannot set alias to a nested join.");
		return $this;
	}

	/**
	 * Forwards call to last operand.
	 *
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function on() {
		if ($last = $this->last()) {
			$args = func_get_args();
			call_user_func_array(array($last, 'init'), $args);
		}
		else
			throw new Kohana_Exception("No operand yet in expression.");
		return $this;
	}

	/**
	 * Forwards call to last operand.
	 *
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function _or() {
		if ($last = $this->last()) {
			$args = func_get_args();
			call_user_func_array(array($last, '_or'), $args);
		}
		else
			throw new Kohana_Exception("No operand yet in expression.");
		return $this;
	}

	/**
	 * Forwards call to last operand.
	 *
	 * @return GlueDB_Fragment_Composite_Join
	 */
	public function _and() {
		if ($last = $this->last()) {
			$args = func_get_args();
			call_user_func_array(array($last, '_and'), $args);
		}
		else
			throw new Kohana_Exception("No operand yet in expression.");
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
		elseif ($name === 'as')
			return call_user_func_array(array($this, '_as'), $args);
	}
}