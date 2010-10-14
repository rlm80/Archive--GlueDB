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
	 * @var GlueDB_Fragment_Composite_Bool On clause.
	 */
	protected $on;

	/**
	 * Constructor.
	 *
	 * @param integer $operator Null means first operand of join expression => no on clause.
	 */
	public function __construct(GlueDB_Fragment $operand, $operator = null) {
		parent::__construct($operand, $operator);
		if (isset($operator)) {
			$this->on = new GlueDB_Fragment_Composite_Bool();
			$this->on->register_user($this);
		}
	}

	/**
	 * Forwards call to on clause.
	 */
	public function init() {
		if (isset($this->operator)) {
			$args = func_get_args();
			call_user_func_array(array($this->on, 'init'), $args);
		}
		else
			throw new Kohana_Exception("Illegal call to init() : no ON clause on first operand of expression !");
	}

	/**
	 * Forwards call to on clause.
	 */
	public function _or() {
		if (isset($this->operator)) {
			$args = func_get_args();
			call_user_func_array(array($this->on, '_or'), $args);
		}
		else
			throw new Kohana_Exception("Illegal call to or() : no ON clause on first operand of expression !");
	}

	/**
	 * Forwards call to on clause.
	 */
	public function _and() {
		if (isset($this->operator)) {
			$args = func_get_args();
			call_user_func_array(array($this->on, '_and'), $args);
		}
		else
			throw new Kohana_Exception("Illegal call to and() : no ON clause on first operand of expression !");
	}

	/**
	 * Compiles the data structure against given database and returns the
	 * resulting SQL string.
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	protected function compile($dbname) {
		$db			= gluedb::db($dbname);
		$operandsql	= $this->operand->sql($dbname);
		$onsql		= isset($this->on) ? $this->on->sql($dbname) : '';
		return $db->compile_operand_join($this->operator, $operandsql, $onsql);
	}
}