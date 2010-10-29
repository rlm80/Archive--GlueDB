<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents an operand in a join expression.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Operand_Join extends GlueDB_Fragment_Operand {
	// Join operators :
	const LEFT_OUTER_JOIN	= 0;
	const RIGHT_OUTER_JOIN	= 1;
	const INNER_JOIN		= 2;

	/**
	 * @var GlueDB_Fragment_Builder_Bool On clause.
	 */
	protected $on;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment $operand
	 * @param integer $operator Null means first operand of join expression => no on clause.
	 */
	public function __construct(GlueDB_Fragment $operand, $operator = null) {
		parent::__construct($operand, $operator);
		$this->on = new GlueDB_Fragment_Builder_Bool();
		$this->on->register_user($this);
	}

	/**
	 *  Returns the on clause, initializing it with given parameters if any.
	 *
	 * @return GlueDB_Fragment_Builder_Bool
	 */
	public function on() {
		if (func_num_args() > 0) {
			$args = func_get_args();
			call_user_func_array(array($this->on, 'init'), $args);
		}
		return $this->on;
	}
}