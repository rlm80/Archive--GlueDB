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
	const _AND		= 0;
	const _OR		= 1;
	const ANDNOT	= 2;
	const ORNOT		= 3;
}