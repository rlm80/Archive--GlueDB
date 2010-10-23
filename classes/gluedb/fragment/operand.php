<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents an operand in an expression.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

abstract class GlueDB_Fragment_Operand extends GlueDB_Fragment {
	/**
	 * @var integer Operator.
	 */
	protected $operator;

	/**
	 * @var GlueDB_Fragment Operand.
	 */
	protected $operand;

	/**
	 * Constructor.
	 *
	 * @param integer $operator Null means first operand.
	 */
	public function __construct(GlueDB_Fragment $operand, $operator = null) {
		$this->operator	= $operator;
		$this->operand	= $operand;
		$this->operand->register_user($this);
	}

	/**
	 * Operator getter/setter.
	 *
	 * @param integer $operator
	 *
	 * @return mixed
	 */
	public function operator($operator = null) {
		if (func_num_args() === 0)
			return $this->operator;
		else {
			$this->operator = $operator;
			$this->invalidate();
			return $this;
		}
	}

	/**
	 * Operand getter/setter.
	 *
	 * @param GlueDB_Fragment $operand
	 *
	 * @return mixed
	 */
	public function operand($operand = null) {
		if (func_num_args() === 0)
			return $this->operand;
		else {
			$this->operand = $operand;
			$this->invalidate();
			return $this;
		}
	}
}