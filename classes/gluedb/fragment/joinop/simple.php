<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents an operand in a join expression made of a single table.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Joinop_Simple extends GlueDB_Fragment_Joinop {
	/**
	 * @var GlueDB_Fragment_Aliased_Table Table - alias pair.
	 */
	protected $operand;

	/**
	 * Constructor.
	 *
	 * @param GlueDB_Fragment_Aliased_Table $operand
	 */
	public function __construct(GlueDB_Fragment_Aliased_Table $operand, $operator = null) {
		parent::__construct($operator);
		$this->operand = $operand;
		$this->operand->register_user($this);
	}

	/**
	 * Operand getter.
	 *
	 * @return GlueDB_Fragment_Aliased_Table
	 */
	public function operand() {
		return $this->operand;
	}

	/**
	 * Returns SQL string for everything that must come before the operator and the "ON".
	 *
	 * @param string $dbname
	 *
	 * @return string
	 */
	protected function compile_operand($dbname) {
		return $operand->sql($dbname);
	}
}