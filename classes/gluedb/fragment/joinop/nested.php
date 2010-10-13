<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents an operand in a join expression made of a nested join expression.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Joinop_Nested extends GlueDB_Fragment_Joinop {
	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment_Join $operand
	 */
	public function __construct(GlueDB_Fragment_Join $operand, $operator = null) {
		parent::__construct($operator);
		$this->operand = $operand;
		$this->operand->register_user($this);
	}

	/**
	 * Returns SQL string for everything that must come before the operator and the "ON".
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	protected function compile_operand($dbname) {
		return '(' . $this->operand->sql($dbname) . ')';
	}
}