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
	 * Operator getter.
	 *
	 * @return integer
	 */
	public function operator() {
		return $this->operator;
	}

	/**
	 * Operand getter.
	 *
	 * @return GlueDB_Fragment
	 */
	public function operand() {
		return $this->operand;
	}
}