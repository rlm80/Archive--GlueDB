<?php defined('SYSPATH') OR die('No direct access allowed.');

/**
 * Fragment that represents an operand in a boolean expression.
 *
 * @package    GlueDB
 * @author     Régis Lemaigre
 * @license    MIT
 */

class GlueDB_Fragment_Operand_Bool extends GlueDB_Fragment_Operand {
	// Boolean operators :
	const _AND	= 0;
	const _OR	= 1;

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
		return $db->compile_operand_bool($this->operator, $operandsql);
	}
}