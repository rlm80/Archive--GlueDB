<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents a boolean expression.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Builder_Bool extends GlueDB_Fragment_Builder {
	/**
	 * @var string Connector redefined.
	 */
	protected $connector = ' ';

	/**
	 * Initializes the expression with a first operand.
	 *
	 * @return GlueDB_Fragment_Builder_Bool
	 */
	public function init() {
		$this->reset();
		$args = func_get_args();
		$this->add($args, null);
		return $this;
	}

	/**
	 * Use ->or() instead of this. Adds a boolean operand at the end of the expression, connecting it with
	 * the OR operator.
	 *
	 * @return GlueDB_Fragment_Builder_Bool
	 */
	public function _or() {
		$args = func_get_args();
		$this->add($args, GlueDB_Fragment_Operand_Bool::_OR);
		return $this;
	}

	/**
	 * Use ->and() instead of this. Adds a boolean operand at the end of the expression, connecting it with
	 * the AND operator.
	 *
	 * @return GlueDB_Fragment_Builder_Bool
	 */
	public function _and() {
		$args = func_get_args();
		$this->add($args, GlueDB_Fragment_Operand_Bool::_AND);
		return $this;
	}
	
	/**
	 * Adds a boolean operand at the end of the expression, connecting it with
	 * the OR NOT operator.
	 *
	 * @return GlueDB_Fragment_Builder_Bool
	 */
	public function ornot() {
		$args = func_get_args();
		$this->add($args, GlueDB_Fragment_Operand_Bool::ORNOT);
		return $this;
	}

	/**
	 * Adds a boolean operand at the end of the expression, connecting it with
	 * the AND NOT operator.
	 *
	 * @return GlueDB_Fragment_Builder_Bool
	 */
	public function andnot() {
		$args = func_get_args();
		$this->add($args, GlueDB_Fragment_Operand_Bool::ANDNOT);
		return $this;
	}	

	/**
	 * Adds an operand to the expression.
	 *
	 * @param array $args
	 * @param integer $operator
	 */
	protected function add($args, $operator) {
		// Get template and replacement values :
		$values	= $args;
		$first	= array_shift($values);

		// Build fragment :
		if ($first instanceof GlueDB_Fragment)
			$fragment = $first;
		else
			$fragment = new GlueDB_Fragment_Template($first, $values);

		// Add operand :
		$this->push(new GlueDB_Fragment_Operand_Bool($fragment, $operator));
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